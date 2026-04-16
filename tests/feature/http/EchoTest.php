<?php

use Dorguzen\Testing\TestCase;

class EchoTest extends TestCase
{
    public function test_posting_json_echoes_back_message()
    {
        $response = $this->post('/echo', [
            'message' => 'hello',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.message', 'hello');
    }
}
