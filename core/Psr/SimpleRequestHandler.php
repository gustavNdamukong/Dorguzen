<?php
namespace Dorguzen\Core\Psr;



/**
 * Implements a very small RequestHandler contract used to chain PSR middlewares.
 * The handler receives the current request and must return whatever the middleware expects.
 * In our hybrid runner we use boolean true to mean "proceed" and false/exception to abort.
 *
 * This is intentionally minimal — its purpose is to call the next middleware in the pipeline.
 */
class SimpleRequestHandler
{
    /** @var callable Returns the result of the next middleware/last callable. Signature: fn(PsrRequestAdapter $req): mixed */
    protected $next;

    public function __construct(callable $next)
    {
        $this->next = $next;
    }

    /**
     * Call the next middleware/handler. Middleware's process() should either:
     *  - call $handler->handle($request) to continue; OR
     *  - throw an exception / return a response-like object to short-circuit.
     */
    public function handle(PsrRequestAdapter $request)
    {
        $callable = $this->next;
        return $callable($request);
    }
}