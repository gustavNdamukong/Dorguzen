<?php

namespace Dorguzen\Tests\Unit;

use Dorguzen\Core\DGZ_DBAdapter;
use Dorguzen\Core\Queues\Drivers\DatabaseQueue;
use Dorguzen\Core\Queues\QueuePayloadSigner;
use PDO;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class QueueSecurityTestJob
{
    public function __construct(public string $name = 'welcome-email') {}
}

/**
 * Runs the queue's real SQL against an in-memory SQLite database.
 */
class QueueSecurityTestDb extends DGZ_DBAdapter
{
    public PDO $pdo;
    private int $rows = 0;

    public function __construct()
    {
        $this->pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $this->pdo->exec('CREATE TABLE dgz_jobs (id INTEGER PRIMARY KEY, queue TEXT, payload TEXT, attempts INT,
            max_attempts INT, reserved_at TEXT, available_at TEXT, created_at TEXT)');
        $this->pdo->exec('CREATE TABLE dgz_failed_jobs (id INTEGER PRIMARY KEY, queue TEXT, payload TEXT, exception TEXT,
            exception_trace TEXT, attempts INT, failed_at TEXT)');
    }

    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);
        $this->rows = $stmt->rowCount();
        return $result;
    }

    public function getAffectedRows(): int
    {
        return $this->rows;
    }

    public function insert(string $table, array $data): bool
    {
        $columns = implode(', ', array_keys($data));
        $marks = implode(', ', array_fill(0, count($data), '?'));
        return $this->execute("INSERT INTO $table ($columns) VALUES ($marks)", array_values($data));
    }
}

class QueueSecurityTest extends TestCase
{
    private QueueSecurityTestDb $db;
    private DatabaseQueue $queue;

    protected function setUp(): void
    {
        ob_start();
        $this->db = new QueueSecurityTestDb();
        $this->queue = new DatabaseQueue($this->db, new QueuePayloadSigner('test-key'));
    }

    protected function tearDown(): void
    {
        ob_end_clean();
    }

    public function test_signer_returns_the_original_job()
    {
        $signer = new QueuePayloadSigner('test-key');

        $job = $signer->verifyAndRestore($signer->sign(new QueueSecurityTestJob('newsletter')));

        $this->assertInstanceOf(QueueSecurityTestJob::class, $job);
        $this->assertSame('newsletter', $job->name);
    }

    public function test_signer_rejects_a_changed_payload()
    {
        $signer = new QueuePayloadSigner('test-key');
        $changed = str_replace('newsletter', 'hacked1234', $signer->sign(new QueueSecurityTestJob('newsletter')));

        $this->expectException(RuntimeException::class);
        $signer->verifyAndRestore($changed);
    }

    public function test_signer_rejects_an_unsigned_payload()
    {
        $this->expectException(RuntimeException::class);
        (new QueuePayloadSigner('test-key'))->verifyAndRestore(serialize(new QueueSecurityTestJob()));
    }

    public function test_signer_rejects_a_payload_signed_with_another_key()
    {
        $other = (new QueuePayloadSigner('other-key'))->sign(new QueueSecurityTestJob());

        $this->expectException(RuntimeException::class);
        (new QueuePayloadSigner('test-key'))->verifyAndRestore($other);
    }

    public function test_signer_requires_a_key()
    {
        $this->expectException(RuntimeException::class);
        new QueuePayloadSigner('');
    }

    public function test_database_queue_returns_a_claimed_job()
    {
        $this->queue->push(new QueueSecurityTestJob('newsletter'));

        $job = $this->queue->pop();

        $this->assertNotNull($job);
        $this->assertSame('newsletter', $job->payload->name);
        $this->assertSame(1, $job->attempts);
        $this->assertGreaterThan(1000000000, $job->availableAt);
    }

    public function test_database_queue_does_not_return_a_reserved_job_twice()
    {
        $this->queue->push(new QueueSecurityTestJob());

        $this->assertNotNull($this->queue->pop());
        $this->assertNull($this->queue->pop());
    }

    public function test_database_queue_moves_a_changed_job_to_failed_jobs()
    {
        $this->queue->push(new QueueSecurityTestJob());
        $this->db->pdo->exec("UPDATE dgz_jobs SET payload = 'abc:O:8:\"stdClass\":0:{}'");

        $this->assertNull($this->queue->pop());
        $this->assertSame(['pending' => 0, 'failed' => 1], $this->queue->stats());
    }

    public function test_failed_job_stays_signed_so_it_can_be_retried()
    {
        $this->queue->push(new QueueSecurityTestJob('newsletter'));
        $this->queue->fail($this->queue->pop(), new RuntimeException('Mail server down'));

        $payload = $this->db->query('SELECT payload FROM dgz_failed_jobs')[0]['payload'];

        $job = (new QueuePayloadSigner('test-key'))->verifyAndRestore($payload);
        $this->assertSame('newsletter', $job->name);
    }
}
