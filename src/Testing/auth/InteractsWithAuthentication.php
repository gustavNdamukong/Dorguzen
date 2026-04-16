<?php

namespace Dorguzen\Testing\Auth;

use Dorguzen\Core\DGZ_Request;

/**
 * InteractsWithAuthentication is a Authentication helper for testing.
 * You will be able to do things like:
 *
 *      $this->actingAs($user)->get('/profile')->assertStatus(200);
 *      $this->actingAsGuest()->get('/dashboard')->assertStatus(302);
 * or
 *      $this->assertGuest();
 */
trait InteractsWithAuthentication
{
    protected ?object $authenticatedUser = null;

    protected string $authGuard = 'default';


    /**
     * Set the currently authenticated user for the request
     */
    public function actingAs(object $user): static
    {
        $this->authenticatedUser = $user;
        return $this;
    }

    /**
     * Remove authentication
     */
    public function actingAsGuest(): static
    {
        $this->authenticatedUser = null;

        return $this;
    }

    /**
     * Apply the authenticated user to the request (framework-aware)
     */
    protected function applyAuthentication(): void
    {
        if ($this->authenticatedUser === null) {
            return;
        }

        $request = container(DGZ_Request::class);

        if (method_exists($request, 'setUser')) {
            $request->setUser($this->authenticatedUser);
        }

        if (method_exists($request, 'setAuthenticatedUser')) {
            $request->setAuthenticatedUser($this->authenticatedUser);
        }
    }
}
