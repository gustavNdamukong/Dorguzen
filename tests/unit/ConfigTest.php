<?php

namespace Dorguzen\Tests\Unit;

use Dorguzen\Testing\TestCase;

class ConfigTest extends TestCase
{
    public function test_config_is_available()
    {
        $this->assertEquals('testing', env('APP_ENV'));
        $this->assertNotNull(config('app'));
    }
}
