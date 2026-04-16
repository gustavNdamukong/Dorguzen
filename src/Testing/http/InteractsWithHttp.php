<?php

namespace Dorguzen\Testing\Http;

/**
 * InteractsWithHttp makes it possible for your tests to read like this:
 *
 *  $this->actingAs($user)
 *       ->withHeaders(['HTTP_ACCEPT' => 'application/json'])
 *       ->get('/dashboard')
 *       ->assertStatus(200)
 *       ->assertSee('Welcome');
 *  Or:
 *
 *  $this->json('POST', '/api/login', [
 *      'email' => 'test@example.com',
 *      'password' => 'secret'
 *  ])->assertJson([
 *      'success' => true
 *  ]);
 */
trait InteractsWithHttp
{

    protected array $defaultHeaders = [];
    protected array $sessionData = [];


    public function withHeaders(array $headers): self
    {
        $this->defaultHeaders = array_merge(
            $this->defaultHeaders,
            $headers
        );

        return $this;
    }


    public function withSession(array $data): self
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        foreach ($data as $key => $value) {
            $_SESSION[$key] = $value;
        }

        return $this;
    }


    /**
    * Reset state between requests (VERY IMPORTANT)
    * Call it at the top of DispatchesHttpRequests->http()
    * the method that actually makes the HTTP request.
    */
    protected function resetHttpState(): void
    {
        $_GET = [];
        $_POST = [];
        $_REQUEST = [];
        $_FILES = [];
        $_SERVER = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
        }

        $this->defaultHeaders = [];
        $this->sessionData = [];
    }
}
