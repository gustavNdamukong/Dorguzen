<?php

namespace Dorguzen\Core\Queues\Drivers;

use Dorguzen\Core\Queues\QueuedJob;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use RuntimeException;
use Throwable;

/**
 * RabbitMQQueue — AMQP-backed queue driver for the Dorguzen Task Scheduler.
 *
 * Requires the php-amqplib package:
 *   composer require php-amqplib/php-amqplib
 *
 * Enable via .env:
 *   QUEUE_DRIVER=rabbitmq
 *
 * How AMQP operations map to QueueInterface:
 *   push()        → basic_publish to the main queue
 *   pop()         → basic_get (non-blocking poll), returns null when empty
 *   acknowledge() → basic_ack — job succeeded, remove it
 *   fail()        → basic_nack (no-requeue) + publish to <queue>.failed
 *   release()     → basic_ack + re-publish with incremented attempts header
 *   stats()       → queue_declare (passive) on main + failed queues
 *   clear()       → queue_purge
 *
 * Delayed jobs:
 *   RabbitMQ has no native per-message delay without the delayed-message plugin.
 *   This driver stores `available_at` in AMQP application_headers and checks it
 *   on pop(). Messages that are not yet ready are nack'd with requeue=true so
 *   they stay in the queue until a later worker poll picks them up.
 *
 * Attempt tracking:
 *   RabbitMQ cannot mutate an in-flight message's body. Attempt counts are
 *   therefore carried in AMQP application_headers (AMQPTable):
 *     attempts, max_attempts, available_at
 *   On release() the original message is ack'd and a new one is published
 *   with an incremented attempts header.
 */
class RabbitMQQueue implements QueueInterface
{
    private ?AMQPStreamConnection $connection = null;

    /** @var \PhpAmqpLib\Channel\AMQPChannel|null */
    private $channel = null;

    public function __construct(
        private string $host,
        private int    $port,
        private string $user,
        private string $password,
        private string $vhost,
        private string $queue       = 'default',
        private int    $maxAttempts = 3,
    ) {
        if (! class_exists(AMQPStreamConnection::class)) {
            throw new RuntimeException(
                "RabbitMQ driver requires php-amqplib.\n" .
                "Install it with:  composer require php-amqplib/php-amqplib"
            );
        }
    }

    // -------------------------------------------------------------------------
    // QueueInterface
    // -------------------------------------------------------------------------

    public function push(object $job, ?int $delaySeconds = null): void
    {
        $headers = new AMQPTable([
            'attempts'     => 0,
            'max_attempts' => $this->maxAttempts,
            'available_at' => time() + ($delaySeconds ?? 0),
        ]);

        $msg = new AMQPMessage(
            serialize($job),
            [
                'delivery_mode'       => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'application_headers' => $headers,
            ]
        );

        $this->channel()->basic_publish($msg, '', $this->queue);
    }


    public function pop(): ?QueuedJob
    {
        $message = $this->channel()->basic_get($this->queue);

        if ($message === null) {
            return null;
        }

        $headerData  = $this->extractHeaders($message);
        $availableAt = (int) ($headerData['available_at'] ?? 0);
        $attempts    = (int) ($headerData['attempts']     ?? 0);
        $maxAttempts = (int) ($headerData['max_attempts'] ?? $this->maxAttempts);

        // Not ready yet — put it back and signal "nothing to run right now"
        if ($availableAt > time()) {
            $this->channel()->basic_nack($message->getDeliveryTag(), false, true);
            return null;
        }

        return new QueuedJob(
            queue:       $this->queue,
            payload:     unserialize($message->getBody()),
            attempts:    $attempts + 1,
            maxAttempts: $maxAttempts,
            availableAt: $availableAt,
            id:          $message->getDeliveryTag(),
        );
    }


    public function acknowledge(QueuedJob $job): void
    {
        $this->channel()->basic_ack($job->id);
    }


    public function fail(QueuedJob $job, Throwable $e): void
    {
        // Remove from the main queue without requeuing
        $this->channel()->basic_nack($job->id, false, false);

        // Archive in the failed queue with error context in headers
        $headers = new AMQPTable([
            'attempts'     => $job->attempts,
            'max_attempts' => $job->maxAttempts,
            'exception'    => substr($e->getMessage(), 0, 512),
            'failed_at'    => time(),
        ]);

        $msg = new AMQPMessage(
            serialize($job->payload),
            [
                'delivery_mode'       => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'application_headers' => $headers,
            ]
        );

        $this->channel()->basic_publish($msg, '', $this->queue . '.failed');
    }


    public function release(QueuedJob $job, int $delaySeconds = 0): void
    {
        // Poison-job protection
        if ($job->attempts >= $job->maxAttempts) {
            $this->fail(
                $job,
                new RuntimeException("Job exceeded max attempts ({$job->maxAttempts})")
            );
            return;
        }

        // Ack the original — it is consumed
        $this->channel()->basic_ack($job->id);

        // Re-publish with the incremented attempt count and new available_at
        $headers = new AMQPTable([
            'attempts'     => $job->attempts,          // already incremented by pop()
            'max_attempts' => $job->maxAttempts,
            'available_at' => time() + $delaySeconds,
        ]);

        $msg = new AMQPMessage(
            serialize($job->payload),
            [
                'delivery_mode'       => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'application_headers' => $headers,
            ]
        );

        $this->channel()->basic_publish($msg, '', $this->queue);
    }


    public function stats(): array
    {
        // queue_declare with passive=true queries without modifying
        $main   = $this->channel()->queue_declare($this->queue,           true);
        $failed = $this->channel()->queue_declare($this->queue . '.failed', true);

        return [
            'pending' => (int) ($main[1]   ?? 0),
            'failed'  => (int) ($failed[1] ?? 0),
        ];
    }


    public function clear(): bool
    {
        $this->channel()->queue_purge($this->queue);
        return true;
    }


    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    /**
     * Lazily opens the AMQP connection and channel, declares both queues.
     * Called by every public method so the connection is made on first use.
     *
     * @return \PhpAmqpLib\Channel\AMQPChannel
     */
    private function channel()
    {
        if ($this->channel === null) {
            $this->connection = new AMQPStreamConnection(
                $this->host,
                $this->port,
                $this->user,
                $this->password,
                $this->vhost
            );

            $this->channel = $this->connection->channel();

            // Declare both queues as durable so they survive broker restarts
            $this->channel->queue_declare($this->queue,               false, true, false, false);
            $this->channel->queue_declare($this->queue . '.failed',   false, true, false, false);
        }

        return $this->channel;
    }


    /**
     * Pull header values out of an AMQPMessage's application_headers property.
     *
     * @param AMQPMessage $message
     * @return array<string, mixed>
     */
    private function extractHeaders(AMQPMessage $message): array
    {
        $props   = $message->get_properties();
        $headers = $props['application_headers'] ?? null;

        return ($headers instanceof AMQPTable) ? $headers->getNativeData() : [];
    }


    public function __destruct()
    {
        try {
            $this->channel?->close();
            $this->connection?->close();
        } catch (Throwable) {
            // Suppress errors during shutdown — the worker may be terminating
        }
    }
}
