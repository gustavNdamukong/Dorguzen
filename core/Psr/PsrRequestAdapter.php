<?php
namespace Dorguzen\Core\Psr;

use Dorguzen\Core\DGZ_Request;


/**
 * Minimal adapter exposing a few convenience methods
 * expected by many PSR-15 middlewares. This is NOT a
 * full PSR-7 implementation — it provides the common
 * methods middleware need (getMethod, getQueryParams,
 * getParsedBody, getHeader, withAttribute/getAttribute).
 *
 * The adapter wraps your existing DGZ_Request so you don't
 * need to re-build requests.
 */
class PsrRequestAdapter
{
    protected DGZ_Request $dgzRequest;
    protected array $attributes = [];

    public function __construct(DGZ_Request $req)
    {
        $this->dgzRequest = $req;
    }

    // Commonly used by middleware
    public function getMethod(): string
    {
        return $this->dgzRequest->method();
    }

    public function getQueryParams(): array
    {
        return $this->dgzRequest->get();
    }

    public function getParsedBody(): array
    {
        return $this->dgzRequest->post();
    }

    public function getHeader(string $name): ?string
    {
        // headers are normalized to e.g. "X-Custom-Header"
        return $this->dgzRequest->header($name);
    }

    // Very small attribute bag so PSR style middleware can store data
    public function withAttribute(string $name, $value): self
    {
        $copy = clone $this;
        $copy->attributes[$name] = $value;
        return $copy;
    }

    public function getAttribute(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    // Helper accessors for convenience (middleware can still access raw request if needed)
    public function raw(): DGZ_Request
    {
        return $this->dgzRequest;
    }
}
