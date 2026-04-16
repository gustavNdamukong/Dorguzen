<?php

namespace Dorguzen\Tests\Feature\Auth;

use Dorguzen\Testing\TestCase;
use Dorguzen\Tests\Support\TestUser;

/**
 * MeTest is used to test the persistence of user data throughout a request lifecycle.
 * Basically when we tell the Dorguzen 'this is the current user', it should not lose,
 * ignore, overwrite, or forget that fact.
 * Why this test is essential before "real auth"
 *
 * Because every future auth feature depends on it:
 *
 *  -middleware like auth
 *  -route guards
 *  -policies
 *  -permissions
 *  -tokens / sessions
 *  -auth()->check()
 *  -auth()->user()
 * If actingAs() doesn't work now, all of that will be broken later, in much harder-to-debug ways.
 * Again, actingAs() does not authenticate a user — it asserts that the framework can carry an auth
 * identity through the request lifecycle. Authentication comes later.
 * This test lays the rails it will run on.
 * In one phrase, here we are testing request auth state awareness.
 */
class MeTest extends TestCase
{
    public function test_authenticated_user_can_access_me_endpoint()
    {
        $testUser = new TestUser();
        $user = $testUser->make([
            'email' => 'test@example.com',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/me');

        $response
            ->assertStatus(200)
            ->assertJson([
                'email' => 'test@example.com',
            ]);
    }
}
