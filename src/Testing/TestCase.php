<?php

namespace Dorguzen\Testing;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Dorguzen\Testing\Http\DispatchesHttpRequests;
use Dorguzen\Testing\Http\InteractsWithHttp;
use Dorguzen\Testing\Auth\InteractsWithAuthentication;


abstract class TestCase extends PHPUnitTestCase
{
    // the RefreshDatabase trait makes DB testing opt-in for test classes
    use RefreshDatabase, DispatchesHttpRequests, InteractsWithHttp, InteractsWithAuthentication;

    /**
     * Indicates if the framework has already been booted.
     */
    protected static bool $frameworkBooted = false;


    /**
     * Set up the test environment before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Safety net (parallel & CLI)
        putenv('APP_ENV=testing');
        $_ENV['APP_ENV'] = 'testing';

        $this->bootFrameworkForTesting();
    }


    /**
     * Boot the Dorguzen framework only once per PHP process.
     *
     * This ensures configs, env, container, helpers, etc are available.
     */
    protected function bootFrameworkForTesting(): void
    {
        if (self::$frameworkBooted) {
            return;
        }

        require_once base_path('bootstrap/testing.php');

        self::$frameworkBooted = true;

        if (method_exists($this, 'refreshDatabase')) {
            $this->refreshDatabase();
        }
    }
}
