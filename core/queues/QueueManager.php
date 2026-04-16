<?php

namespace Dorguzen\Core\Queues;

use Dorguzen\Config\Config;
use Dorguzen\Core\Queues\Drivers\DatabaseQueue;
use Dorguzen\Core\Queues\Drivers\RabbitMQQueue;
use Dorguzen\Core\Queues\Drivers\SyncQueue;
use Dorguzen\Core\Queues\Drivers\QueueInterface;

/** 
 * QueueManager is where:
 *   -env/config drives behavior
 *   -async becomes opt-in
 */
class QueueManager
{
    /**
     * queue will always represent the currently applicable queue 
     * as defined in config ('QUEUE_DRIVER') e.g. 
     * DatabaseQueue or SyncQueue which all implement QueueInterface
     * @var QueueInterface
     */
    protected QueueInterface $queue;

    public function __construct(
        protected Config $config,
        protected \Dorguzen\Core\DGZ_Container $container
    ) {
        $this->queue = $this->resolveQueue();
    }

    protected function resolveQueue(): QueueInterface
    {
        $driver = $this->config->get('app.queue_driver');

        return match ($driver) {
            'sync'     => $this->container->get(SyncQueue::class),
            'db'       => $this->container->get(DatabaseQueue::class),
            'rabbitmq' => new RabbitMQQueue(
                host:     (string) ($this->config->get('app.rabbitmq.host')     ?? '127.0.0.1'),
                port:     (int)    ($this->config->get('app.rabbitmq.port')     ?? 5672),
                user:     (string) ($this->config->get('app.rabbitmq.user')     ?? 'guest'),
                password: (string) ($this->config->get('app.rabbitmq.password') ?? 'guest'),
                vhost:    (string) ($this->config->get('app.rabbitmq.vhost')    ?? '/'),
                queue:    (string) ($this->config->get('app.rabbitmq.queue')    ?? 'default'),
            ),
            default => throw new \RuntimeException("Unsupported queue driver [$driver]")
        };
    }

    /**
     * push a job to a queue e.g. 
     * 
     *      $queue = container(QueueManager::class);
     *      $queue->push(new TestSyncJob());
     * 
     * @param object $job can be a job, event etc
     * @param mixed $delaySeconds
     * @return void
     */
    public function push(object $job, ?int $delaySeconds = null): void
    {
        $this->queue->push($job, $delaySeconds);
    }

    public function pop(): ?QueuedJob
    {
        return $this->queue->pop();
    }

    public function acknowledge(QueuedJob $job): void
    {
        $this->queue->acknowledge($job);
    }

    public function fail(QueuedJob $job, \Throwable $e): void
    {
        $this->queue->fail($job, $e);
    }


    public function release(QueuedJob $job, int $delaySeconds = 0): void
    {
        $this->queue->release($job, $delaySeconds);
    }


    public function stats(): array
    {
        return $this->queue->stats();
    }


    public function clear(): bool
    {
        return $this->queue->clear();
    }
}