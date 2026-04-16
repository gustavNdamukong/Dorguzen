<?php 

namespace Dorguzen\Core\Events;


/**
 * EventService is the DGZ entry point for events. The flow goes like this:
 *
 *  EventService dispatches a service to EventDispatcher, 
 *  EventDispatcher resolves listeners from the central glue (config), 
 *   then calls handle() on each listener to handle the event.
 */
class EventService
{
    public function __construct(
        protected EventDispatcher $dispatcher
    ) {}

    public function dispatch(object $event): void
    {
        $this->dispatcher->dispatch($event);
        //--------------------------------------------------
       
        /*echo "Inside in EventService , about to dispatch the event\n";
        try {
            $this->dispatcher->dispatch($event);
        }
        catch(\Throwable $e)
        {
            echo $e->getMessage()."\n";
        }
            echo "EventService dispatch successful\n";*/
        
        //--------------------------------------------------
    }
}