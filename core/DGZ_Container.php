<?php
namespace Dorguzen\Core;

use ReflectionClass;
use ReflectionParameter;
use Exception;

/**
 * This is a Dependency Injection Container (DIC) 
 * It fully supports recursive (nested) dependency resolution, meaning it will resolve the 
 * subdependencies of any dependency until every required class has been instantiated.
 * This ability is often referred to as reflection-based autowiring (and recursive 
 * dependency resolution).
 * 
 * Things to keep in mind:
 *  Circular dependencies (A → B → A) will cause infinite recursion.
 *  Try to design dependencies one-way: lower-level services don’t depend on higher-level ones.
 *  For scalar constructor params (like strings or numbers), you’ll need defaults, config injections, 
 *  or setter methods.
 *  If a dependency isn’t type-hinted, the container won’t know how to resolve it — so everything must 
 *  be type-hinted properly.
 *
 * Example usage:
 *  $container = new Dorguzen\Core\DGZ_Container();
 *  $response = $container->get(Dorguzen\Core\DGZ_Response::class);
 *  $authService = $container->get(App\Services\AuthService::class);
 * 
 * To make your entire app have access to the container, place the following two lines in something like 
 * the index.php that is the landing page of your whole application 
 * (this is an approach used by frameworks, in a design pattern known as the Front Controller) 
 *      $container = new DGZ_Container();
 *      $GLOBALS['container'] = $container;
 */
class DGZ_Container
{
    /**
     * $bindings are blueprints (factories) aka closures (callables)
     * @var array
     */
    protected array $bindings = [];

    /**
     * $instances are actual resolved objects. After get() calls the closure 
     *  in (stored in $bindings above) it will store the resolved object here 
     *  in $instances.
     * 
     * This allows container to cache singletons
     * It makes every class resolved by the container a singleton by default
     * (unless you explicitly unset it or rebind it).  
     * @var array
     */
    protected array $instances = [];


    protected $resolving = [];


    private array $reflectionCache = [];


    public function set(string $abstract, callable $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    // for ergonomics here's another way to do what set() does
    public function bind(string $abstract, callable $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }


    /**
     * Summary of get
     * @param string $abstract the object the app has requested to resolve by FQCN
     * @param array $parameters
     * @throws Exception
     */
    public function get(string $abstract, array $parameters = [])
    {
        // Normalize class name — supports leading backslashes or inconsistent casing
        $abstract = ltrim($abstract, '\\');

        // ✅ 1. Return cached instance if it exists
        // Here we are reusing existing instance of the object ($abstract)
        if (isset($this->instances[$abstract]) && is_object($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // ✅ 2. Return bound factory instance if defined
        // Here we are resolving the object ($abstract) from container (binding)
        if (isset($this->bindings[$abstract])) {
            $object = $this->bindings[$abstract]($this);
            $this->instances[$abstract] = $object;
            return $object;
        }

        // 🚫 4. Ensure class exists
        if (!class_exists($abstract)) {
            throw new Exception("Cannot resolve: {$abstract}");
        }

        // 🛡️ 5. Initialize recursion guard
        if (!isset($this->resolving)) {
            $this->resolving = [];
        }

        // 🔒 6. Detect circular dependencies
        if (isset($this->resolving[$abstract])) {
            error_log("⚠️ [Container] Circular dependency detected while resolving: {$abstract}");
            throw new Exception("Circular dependency detected for {$abstract}");
        }

        // 🔄 7. Mark class as being resolved
        $this->resolving[$abstract] = true;

        // 🧠 8. Use reflection to autowire dependencies, but cache it
        if (!isset($this->reflectionCache[$abstract])) {
            $this->reflectionCache[$abstract] = new ReflectionClass($abstract);
        }
        $reflection = $this->reflectionCache[$abstract];

        if (!$reflection->isInstantiable()) {
            unset($this->resolving[$abstract]);
            throw new Exception("Cannot instantiate abstract type: {$abstract}");
        }

        // 9. Get the constructor, if any
        $constructor = $this->getConstructor($reflection);

        // 10. If no constructor, just create a new instance
        if (!$constructor) {
            $object = new $abstract();
            unset($this->resolving[$abstract]);
            $this->instances[$abstract] = $object;
            return $object;
        }

        // 11. Resolve constructor dependencies (including parent constructors)
        $dependencies = !empty($parameters)
            ? $parameters
            : $this->resolveDependencies($constructor->getParameters(), $parameters);

        // 12. Instantiate class with dependencies
        $object = $reflection->newInstanceArgs($dependencies);

        // 13. Resolving is done, so cache and cleanup
        $this->instances[$abstract] = $object;
        unset($this->resolving[$abstract]);

        return $object;
    }


    protected function resolveParameter(ReflectionParameter $param)
    {
        $type = $param->getType();

        // If it's a class (non-builtin), try to resolve through the container
        if ($type && !$type->isBuiltin()) {
            $dependency = $type->getName();

            // 🛡️ Respect the container’s recursion guard
            // skip if the dependency (object) is already being resolved
            if (isset($this->resolving[$dependency])) {
                // ✅ It's OK to return null here (single param)
                return null; // Prevent recursion — return null or a stub
            }

            return $this->get($dependency);
        }

        // Fallback for scalar parameters with default values
        if ($param->isDefaultValueAvailable()) {
            return $param->getDefaultValue();
        }

        throw new Exception("Cannot resolve parameter \${$param->getName()}");
    }


    /**
     * Create a singleton instance for $abstract
     * @param string $abstract
     * @param callable $factory
     * @return void
     */
    public function singleton(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = function ($container) use ($factory, $abstract) {
            static $instance = null;
            if ($instance === null) {
                $instance = $factory($container);
            }
            return $instance;
        };
    }

    /**
     * Finds the deepest constructor up the inheritance chain
     */
    protected function getConstructor(ReflectionClass $reflection)
    {
        while ($reflection) {
            $constructor = $reflection->getConstructor();
            if ($constructor) {
                return $constructor;
            }
            $reflection = $reflection->getParentClass();
        }
        return null;
    }

    /**
     * Resolve dependencies recursively, using the container if needed
     */
    protected function resolveDependencies(array $parameters, array $provided)
    {
        $dependencies = [];

        foreach ($parameters as $param) {
            $name = $param->getName();
            $type = $param->getType();

            // Use manually provided argument if given
            if (array_key_exists($param->getPosition(), $provided)) {
                $dependencies[] = $provided[$param->getPosition()];
                continue;
            }

            // If type is a class (non-builtin), try to resolve recursively through the container
            if ($type && !$type->isBuiltin()) {
                $dependencyClassName = $type->getName();

                // 🛡️ Respect the container’s recursion guard
                // skipping because this object ($dependencyClassName) is already being resolved
                if (isset($this->resolving[$dependencyClassName])) {
                    // Prevent recursion — return null or a stub
                    $dependencies[] = null; 
                    continue;
                }

                $dependencies[] = $this->get($dependencyClassName);
            }
            // Optional parameters
            elseif ($param->isDefaultValueAvailable()) {
                $dependencies[] = $param->getDefaultValue();
            }
            // If nothing else, pass null
            else {
                $dependencies[] = null;
            }
        }

        return $dependencies;
    }



    /**
     * has() can be used to quickly check if a class is resolvable by the container 
     * before trying to pull it from the container.
     * It it returns true, the desired class can then be pulled (resolved) from the 
     * container using get(). 
     * Example use:
     * 
     *      if (container()->has(myClass::class))
     *      {
     *          $myClass = container(myClass::class);
     *      } else {
     *          echo "{$myClass} cannot be resolved";
     *      }
     * 
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        // Already resolved or bound
        if (isset($this->instances[$id]) || isset($this->bindings[$id])) {
            return true;
        }

        // Autowirable class?
        return class_exists($id);
    }


    /**
     * Resolves a class by its short (unqualified) name when the full FQCN is not known.
     * Searches registered bindings and cached instances for a class whose base name matches.
     * Used by DGZ_Model's relationship system to support short-name keys in $_hasChild / $_hasParent.
     *
     * @param  string      $shortName  e.g. 'Orders', 'Products'
     * @return object|null             The resolved instance, or null if not found
     */
    public function getByShortName(string $shortName): ?object
    {
        $shortNameLower = strtolower($shortName);

        // Search bindings (registered singletons / factories)
        foreach (array_keys($this->bindings) as $fqcn) {
            $parts = explode('\\', $fqcn);
            if (strtolower(end($parts)) === $shortNameLower) {
                return $this->get($fqcn);
            }
        }

        // Search already-resolved instances
        foreach (array_keys($this->instances) as $fqcn) {
            $parts = explode('\\', $fqcn);
            if (strtolower(end($parts)) === $shortNameLower) {
                return $this->get($fqcn);
            }
        }

        return null;
    }
}