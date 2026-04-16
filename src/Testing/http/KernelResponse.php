<?php

namespace Dorguzen\Testing\Http;

class KernelResponse
{
    protected int $status;
    protected array $headers = [];
    protected string $body;

    public function __construct(int $status, array $rawHeaders, string $body)
    {
        $this->status = $status;
        $this->headers = $this->normalizeHeaders($rawHeaders);
        $this->body = $body;
    }

    protected function normalizeHeaders(array $raw): array
    {
        $headers = [];

        foreach ($raw as $headerLine) {
            if (strpos($headerLine, ':') === false) {
                continue;
            }

            [$name, $value] = explode(':', $headerLine, 2);
            $headers[trim($name)] = trim($value);
        }

        return $headers;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
