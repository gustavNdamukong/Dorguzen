<?php

namespace Dorguzen\Core\JetForms;

/**
 * FormRegistry a registry that can be used by middleware to map _form_name => class. 
 * It has two modes:
*
*   -Auto-discover: scan src/forms for classes extending JetForms and call ->name to register.
*   -Manual register API.
* 
*/
class JetFormsRegistry
{
    protected array $map = [];

    /**
     * Register a form class for a given name
     * @param string $name unique form name
     * @param string $class FQCN of form class
     */
    public function register(string $name, string $class): void
    {
        $this->map[$name] = $class;
    }

    /**
     * Resolve a form instance by name. Returns null if not found.
     */
    public function resolve(string $name): ?JetForms
    {
        if (!isset($this->map[$name])) {
            return null;
        }
        $class = $this->map[$name];
        if (!class_exists($class)) {
            return null;
        }
        return new $class();
    }

    /**
     * Auto-discover forms under src/forms (simple implementation).
     * It expects each form file to declare a class in namespace Dorguzen\Forms and be PSR-4 autoloadable.
     * Call this during bootstrap (or the middleware will call it lazily).
     * 
     * Where to call discover(): in bootstrap or first call in middleware. If you prefer to manually register forms, 
     * call container(FormRegistry::class)->register('contact', \src\forms\ContactForm::class); in bootstrap.
     */
    public function discover(string $formsPath): void
    {
        // quick and pragmatic: glob all php files and attempt to instantiate
        $files = glob($formsPath . '/*.php');
        foreach ($files as $file) {
            $contents = file_get_contents($file);
            if (preg_match('/namespace\s+([^;]+);/m', $contents, $m)) {
                $namespace = trim($m[1]);
            } else {
                $namespace = 'src\\forms';
            }
            $className = basename($file, '.php');
            $fqcn = $namespace . '\\' . $className;
            if (!class_exists($fqcn)) {
                require_once $file; // fallback if not autoloaded
            }
            if (class_exists($fqcn)) {
                $ref = new \ReflectionClass($fqcn);
                if ($ref->isInstantiable() && $ref->isSubclassOf(JetForms::class)) {
                    $instance = $ref->newInstance();
                    if (!empty($instance->name)) {
                        $this->register($instance->name, $fqcn);
                    }
                }
            }
        }
    }
}