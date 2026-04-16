<?php

/**
 * Event → Listener map.
 *
 * Each key is a fully-qualified event class. Each value is an array of
 * listener classes that will be called when that event is dispatched.
 *
 * Listeners run in the order listed. A listener that implements
 * Dorguzen\Core\Events\ShouldQueue is pushed onto the queue instead of
 * running immediately.
 *
 * To add your own events:
 *   1. php dgz make:event   MyEvent
 *   2. php dgz make:event   MyEventListener   (or create a listener manually)
 *   3. Register the mapping here.
 */

return [

    // -------------------------------------------------------------------------
    // User lifecycle events — dispatched automatically by Dorguzen
    // -------------------------------------------------------------------------

    \Dorguzen\Events\UserRegistered::class => [
        \Dorguzen\Listeners\SendWelcomeEmail::class,
        \Dorguzen\Listeners\LogUserRegistration::class,
    ],

    \Dorguzen\Events\UserLoggedIn::class => [
        \Dorguzen\Listeners\LogUserLogin::class,
    ],

    \Dorguzen\Events\UserLoggedOut::class => [
        \Dorguzen\Listeners\LogUserLogout::class,
    ],

    // -------------------------------------------------------------------------
    // Engagement events — dispatched automatically by Dorguzen
    // -------------------------------------------------------------------------

    \Dorguzen\Events\UserSubscribed::class => [
        \Dorguzen\Listeners\SendSubscriptionConfirmation::class,
    ],

    \Dorguzen\Events\ContactFormSubmitted::class => [
        \Dorguzen\Listeners\SendContactConfirmation::class,
    ],

];
