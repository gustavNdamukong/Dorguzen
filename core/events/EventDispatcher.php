<?php 

namespace Dorguzen\Core\Events;

use Dorguzen\Core\Queues\QueueManager;

/**
 * EventDispatcher is the core events engine.
 * It's the centerpiece, that makes the events system work.
 * EventService dispatches a service (event) to it, it resolves 
 * listener classes from config (the central glue), then calls handle() 
 * on each listener to act on the event.
 */
class EventDispatcher
{
    public function __construct(
        protected ListenerResolver $resolver,
        protected QueueManager $queue
    ) {}

    public function dispatch(object $event): void
    {
        $eventClass = get_class($event);

        $listeners = $this->resolver->resolve($eventClass);

        foreach ($listeners as $listener) {
            $this->callListener($listener, $event);
        }
    }

    protected function callListener(object $listener, object $event): void
    {
        if (!method_exists($listener, 'handle')) {
            throw new \RuntimeException(
                sprintf(
                    'Listener [%s] has no handle() method',
                    get_class($listener)
                )
            );
        }

        if ($listener instanceof ShouldQueue) {
            echo "Listener queued: " . get_class($listener) . "\n";
            $this->handleQueuedListener($listener, $event);
        } else {
            $this->handleSyncListener($listener, $event);
        }
    }


    protected function handleSyncListener(object $listener, object $event): void
    {
        $listener->handle($event);
    }

    protected function handleQueuedListener(object $listener, object $event): void
    {
        // Delegate execution strategy entirely to the queue system

        // So this event listener's handle() is not immediately ran, but rather,
        // passed into the queue system which will decide if it's meant 
        // for the sync queue (and be ran immediately), or if it's meant for the async queue 
        // (represented in dgz by DatabaseQueue) and be queued in the DB to be ran later.
        // Events are passed in to the queue system using the Events/Queue bridge, so-to-speak, 
        // which is the Dorguzen\Core\Queues\QueueManager. Basically, you just have to 
        // instantiate the QueueManager, and call push() on it. Instantiating QueueManager 
        // does two things:
        //      -checks the config setting 'app.queue_driver' for the defined queue type (QueueInterface)
        //      -it sets its queue property to that interface type (which can be either SyncQueue or DatabaseQueue).
        // Next, you call push() on the given queue driver (its $queue property). 
        // In the case of SyncQueue, $this->queue->push($job, $delaySeconds); calls handle() on the given Event immediately 
        // In the case of DatabaseQueue, $this->queue->push($job, $delaySeconds); saves the object in a queue (eg dgz_jobs DB table). 
        // From this observation, it means, because some events can implement ShouldQueue, they can be queued.
        // Also, of the events whose listeners can be queued, there are two ways they can be run; either immediately 
        // if the current user-defined 'app.queue_driver' config settings is 'sync', or later 
        // if the current user-defined 'app.queue_driver' config settings is 'queue'.
        // If this is true, then the ShouldQueue object should stay, but then to ensure those events
        // that implement ShouldQueue are sent to the queue system, this handleQueuedListener() needs to be 
        // modified so it delegates their handling to the QueueManager and its push() method. 
        //
        // The other observation is that the Events system is separate from the Queue system, but 
        //      -Events can be queued, in which case they become queued jobs
        //      -Jobs can be queued 
        // This is an accurate summary
        $this->queue->push(new QueuedListener($listener, $event));
    }
}