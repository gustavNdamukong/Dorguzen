<?php 

namespace Dorguzen\Core\Events;

/**
 * This is the bridge between events and jobs (tasks).
 * While jobs are always dispatched/pushed to a queue be 
 * consumed (executed) later, Events can both either be 
 * executed immediately or dispatched/pushed to a queue 
 * to be executed later. ShouldQueue is the interface that 
 * decides whether an event will be placed in queue like 
 * a job for later execution. Placiong it in the queue 
 * automatically makes Events to be treated like jobs, 
 * and hence will follow the same queue pipeline as jobs.
 * This means only Event Listeners of Events intended to be 
 * queued will implement ShouldQueue. 
 */
interface ShouldQueue
{
    // marker only — no methods
}