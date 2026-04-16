<?php

use Dorguzen\Testing\TestCase;

class JsonHelperTest extends TestCase
{
    public function test_json_helper_sets_json_request()
    {
        $response = $this->json('POST', '/echoJson', [
            'name' => 'Gustav',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Gustav',
                ],
            ]);
    }
}
