<?php

namespace Dorguzen\Core;


class DGZ_RouteDefinition
{

    protected DGZ_Router $router;


    /**
     * Reference to the actual route array stored in the router.
     * Any mutation here affects the router's route list.
     */
    protected array $route;

    public function __construct(DGZ_Router &$router, array &$route)
    {
        $this->router = &$router;
        // its important to assign by reference here
        $this->route = &$route;
    }

    /**
     * Assign a name to the route.
     */
    public function name(string $name): self
    {
        $this->route['name'] = $name;

        $this->router->registerNamedRoute($name, $this->route); 

        return $this;
    }
}