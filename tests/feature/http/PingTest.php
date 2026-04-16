<?php

namespace Dorguzen\Tests\Feature\Http;

use Dorguzen\Testing\TestCase;


class PingTest extends TestCase
{
    /**
     * test_ping_endpoint_returns_ok will confirm the following if it passes
     * and the opposite if it fails:
     *
     *      -Routing is working
     *      -Kernel dispatch is working
     *      -JSON decoding is working
     *
     * @return void
     */
    public function test_ping_endpoint_returns_ok()
    {
        $response = $this->get('/ping');

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
            ]);
    }
}
