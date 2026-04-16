<?php 

namespace Dorguzen\Core\Events;

/**
 * QueuedListener is the skeleton of an event listener that 
 * will be used to queue jobs, by core/queues/QueueManager.php,
 * dispatched (delegated) to it by core/events/EventDispatcher.php
 */
class QueuedListener
{
    public function __construct(
        protected object $listener,
        protected object $event
    ) {}

    public function handle(): void
    {
        $this->listener->handle($this->event);
    }
}