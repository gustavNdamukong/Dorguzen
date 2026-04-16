<?php

use Dorguzen\Testing\TestCase;

/**
 * AuthGuardTest In one phrase, here we are testing request auth state awareness.
 * This controller must ask the request:
 *      -do we have a user?
 *      -if no, return a 401
 *      -if yes, return user data
 *
 * This test is the exact opposite of the MeTest test in 'tests/feature/Auth/MeTest'.
 * It is meant to receive a 401 status back because the user in the request in MeTest
 * is no longer supposed to still exist. It verifies three critical framework guarantees:
 *
 *  -1) Auth state resets between requests
 *      -actingAs() sets a user only for that test
 *      -Next test starts clean
 *      -No leakage
 *
 *  -2) Request lifecycle carries auth context
 *      -DispatchesHttpRequests → DGZ_Request
 *      -request()->user() reflects injected state
 *
 *  -3) Controllers can safely enforce access
 *      -Controller logic is simple
 *      -No framework magic needed
 *      -Predictable behavior
 */
class AuthGuardTest extends TestCase
{
    public function test_guest_cannot_access_me_endpoint()
    {
        $response = $this->get('/me');

        $response->assertStatus(401);
    }
}
