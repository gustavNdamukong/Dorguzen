<?php 

namespace Dorguzen\Core\Events;


use Dorguzen\Core\DGZ_Container;

class ListenerResolver
{
    public function __construct(
        protected array $eventsConfig,
        protected DGZ_Container $container
    ) {
        if ($eventsConfig === null) {
            throw new \RuntimeException(
                "Events config is missing. Expected config('events') to return an array."
            );
        }

        $this->eventsConfig = $eventsConfig;
    }

    public function resolve(string $eventClass): array
    {
        $listeners = $this->eventsConfig[$eventClass] ?? [];

        return array_map(
            fn ($listener) => $this->container->get($listener),
            $listeners
        );
    }
}