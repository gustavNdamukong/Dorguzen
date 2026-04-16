<?php

namespace Dorguzen\Testing\Http;

use PHPUnit\Framework\Assert;

/**
 * TestResponse contains Dorguzen framework-level assertions, not PHPUnit's raw ones.
 * This class supports the following:
 *
 *      Method	                    Purpose
 *      assertJson()	            Subset match
 *      assertJsonPath()	        Precise value
 *      assertJsonCount()	        Array size
 *      assertExactJson()	        Strict equality
 *      assertJsonMissing()	        Absence check
 *      assertJsonFragment()	    Loose fragment
 *
 */
class TestResponse
{
    protected object $response;

    protected int $status;
    protected array $headers;
    protected string $body;
    protected ?object $originalResponse;

    public function __construct(object $response)
    {
        $this->response = $response;
    }

    /**
     * Get raw response (escape hatch)
     */
    public function getOriginalResponse(): object
    {
        return $this->response;
    }

    /**
     * Assert HTTP status code
     */
    public function assertStatus(int $expected): self
    {
        $actual = $this->response->getStatusCode();

        Assert::assertSame(
            $expected,
            $actual,
            "Expected status code {$expected}, got {$actual}."
        );

        return $this;
    }

    /**
     * Assert response body contains a string
     */
    public function assertSee(string $value): self
    {
        Assert::assertStringContainsString(
            $value,
            (string) $this->response->getBody(),
            "Response body does not contain expected string."
        );

        return $this;
    }

    /**
     * Assert response body does NOT contain a string
     */
    public function assertDontSee(string $value): self
    {
        Assert::assertStringNotContainsString(
            $value,
            (string) $this->response->getBody(),
            "Response body contains unexpected string."
        );

        return $this;
    }

    /**
     * Assert response header exists (optionally with value)
     */
    public function assertHeader(string $name, ?string $expectedValue = null): self
    {
        $headers = $this->response->getHeaders();

        Assert::assertArrayHasKey(
            $name,
            $headers,
            "Header [{$name}] not found in response."
        );

        if ($expectedValue !== null)
        {
            $actual = $headers[$name];

            if (is_array($actual))
            {
                Assert::assertContains($expectedValue, $actual);
            }
            else
            {
                Assert::assertSame($expectedValue, $actual);
            }
        }

        return $this;
    }

    /**
     * Decode JSON body safely
     */
    protected function decodeJson(): array
    {
        $body = (string) $this->response->getBody();

        $decoded = json_decode($body, true);

        Assert::assertNotNull(
            $decoded,
            'Response does not contain valid JSON:' . $body
        );

        return $decoded;
    }

    /**
     * Assert JSON contains subset
     */
    public function assertJson(array $subset): self
    {
        $json = $this->decodeJson();

        foreach ($subset as $key => $value) {
            Assert::assertArrayHasKey(
                $key,
                $json,
                "JSON key [{$key}] not found."
            );

            Assert::assertEquals(
                $value,
                $json[$key],
                "JSON value mismatch for key [{$key}]."
            );
        }

        return $this;
    }


    public function assertOk(): self
    {
        return $this->assertStatus(200);
    }

    public function assertCreated(): self
    {
        return $this->assertStatus(201);
    }

    public function assertNoContent(): self
    {
        return $this->assertStatus(204);
    }

    public function assertNotFound(): self
    {
        return $this->assertStatus(404);
    }

    public function assertForbidden(): self
    {
        return $this->assertStatus(403);
    }

    public function assertUnauthorized(): self
    {
        return $this->assertStatus(401);
    }

    public function assertServerError(): self
    {
        Assert::assertTrue(
            $this->response->getStatusCode() >= 500,
            'Response is not a server error.'
        );

        return $this;
    }


    //--------------------------------------------
    //  Content-Type assertions (VERY common)
    //--------------------------------------------

    public function assertJsonResponse(): self
    {
        return $this->assertHeader('Content-Type', 'application/json');
    }

    public function assertHtmlResponse(): self
    {
        return $this->assertHeader('Content-Type', 'text/html');
    }


    //--------------------------------------------
    //  Empty body assertions
    //--------------------------------------------

    public function assertEmpty(): self
    {
        Assert::assertEmpty(
            (string) $this->response->getBody(),
            'Response body is not empty.'
        );

        return $this;
    }



    //--------------------------------------------
    //  JSON structure assertion (simple but powerful)
    //  This is not deep schema validation — just shape
    //  checking.
    /*
    *   Example usage:
    *       $response->assertJsonStructure([
    *            'id',
    *            'email',
    *            'created_at',
    *        ]);
    */
    //--------------------------------------------

    public function assertJsonStructure(array $structure): self
    {
        $json = $this->decodeJson();

        foreach ($structure as $key) {
            Assert::assertArrayHasKey(
                $key,
                $json,
                "JSON key [{$key}] not found."
            );
        }

        return $this;
    }


    //--------------------------------------------
    //  Error message assertion (common in APIs)
    //--------------------------------------------

    public function assertError(string $message): self
    {
        $json = $this->decodeJson();

        Assert::assertTrue(
            isset($json['error']) || isset($json['message']),
            'No error message found in JSON response.'
        );

        Assert::assertStringContainsString(
            $message,
            $json['error'] ?? $json['message']
        );

        return $this;
    }


    /**
     * assertJsonPath() is the most important helper.
     * It asserts a value at a dot-notated JSON path e.g.
     *
     *  $response->assertJsonPath('data.user.email', 'test@example.com');
     *
     * @param string $path
     * @param mixed $expected
     * @return TestResponse
     */
    public function assertJsonPath(string $path, mixed $expected): self
    {
        $json = $this->decodeJson();

        $segments = explode('.', $path);
        $current = $json;

        foreach ($segments as $segment) {
            Assert::assertIsArray(
                $current,
                "JSON path [{$path}] is invalid."
            );

            Assert::assertArrayHasKey(
                $segment,
                $current,
                "JSON path segment [{$segment}] not found in path [{$path}]."
            );

            $current = $current[$segment];
        }

        Assert::assertEquals(
            $expected,
            $current,
            "JSON path [{$path}] value mismatch."
        );

        return $this;
    }


    /**
     * assertJsonCount() lets you assert array size at root or path.
     * You would be able to do this:
     *
     *      $response->assertJsonCount(3);
     *      $response->assertJsonCount(5, 'data.items');
     *
     * @param int $expectedCount
     * @param mixed $path
     * @return TestResponse
     */
    public function assertJsonCount(int $expectedCount, ?string $path = null): self
    {
        $json = $this->decodeJson();

        if ($path !== null) {
            $segments = explode('.', $path);
            foreach ($segments as $segment) {
                Assert::assertArrayHasKey(
                    $segment,
                    $json,
                    "JSON path [{$path}] not found."
                );
                $json = $json[$segment];
            }
        }

        Assert::assertIsArray(
            $json,
            'JSON value is not countable.'
        );

        Assert::assertCount(
            $expectedCount,
            $json,
            "JSON count mismatch."
        );

        return $this;
    }


    /**
     * assertExactJson() is used to enforce strict equality.
     * It asserts exact structure and values — no extra keys allowed.
     * You would be able to do this:
     *
     *    $response->assertExactJson([
     *        'success' => true,
     *        'data' => null,
     *    ]);
     *
     * @param array $expected
     * @return TestResponse
     */
    public function assertExactJson(array $expected): self
    {
        $actual = $this->decodeJson();

        Assert::assertSame(
            $expected,
            $actual,
            'JSON does not exactly match expected structure.'
        );

        return $this;
    }


    /**
     * assertJsonMissing() is used to assert that a key is not present.
     * Example usage:
     *
     *      $response->assertJsonMissing('password');
     *      $response->assertJsonMissing('data.secret');
     *
     * @param string $path
     * @return TestResponse
     */
    public function assertJsonMissing(string $path): self
    {
        $json = $this->decodeJson();
        $segments = explode('.', $path);

        foreach ($segments as $segment) {
            if (!is_array($json) || !array_key_exists($segment, $json)) {
                // Missing is OK
                return $this;
            }
            $json = $json[$segment];
        }

        Assert::fail("JSON path [{$path}] exists but should be missing.");
    }


    public function assertJsonFragment(array $fragment): self
    {
        $json = json_encode($this->decodeJson());
        $needle = json_encode($fragment);

        Assert::assertStringContainsString(
            trim($needle, '{}'),
            $json,
            'JSON fragment not found.'
        );

        return $this;
    }



    //--------------------------------------------
    //  AUTH HELPERS
    // They work in collaboration with the InteractsWithAuthentication
    // Dorguzen\Testing\Auth\InteractsWithAuthentication trait
    //--------------------------------------------

    public function assertAuthenticated(): self
    {
        Assert::assertTrue(
            auth()->check(),
            'Expected user to be authenticated.'
        );

        return $this;
    }

    public function assertGuest(): self
    {
        Assert::assertFalse(
            auth()->check(),
            'Expected user to be a guest.'
        );

        return $this;
    }

}
