<?php

namespace Dorguzen\Testing\Http;

use Dorguzen\Core\Kernel\HttpKernel;
use Dorguzen\Core\DGZ_Request;


/**
 * This class is to work in testing only.
 * It mutates PHP globals / streams.
 * It should never run in production.
 * It is what defines get(), post(), json(), etc.
 * Once TestCase uses it, all feature tests automatically
 * get access to its methods.
 */
trait DispatchesHttpRequests
{
    /**
     * http() makes HTTP requests through the same DGZ_ROUTER as web HTTP requests
     * @param string $method
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return TestResponse
     */
    protected function http(
        string $method,
        string $uri,
        array $data = [],
        array $headers = []
    ): TestResponse
    {

        // Reset state between requests
        $this->resetHttpState();

        $headers = array_merge($this->defaultHeaders ?? [], $headers);
        $this->prepareServerVariables($method, $uri, $headers);
        $this->prepareRequestData($method, $data);

        // apply authentication
        $this->applyAuthentication();

        // if there's any test user created in tests, add it to the global request object
        if ($this->authenticatedUser !== null) {
            $request = container(DGZ_Request::class);
            $request->setUser($this->authenticatedUser);
        }

        // dispatch kernel
        $kernel = new HttpKernel();
        $response = $kernel->dispatchForTesting();


        //-------------------------------------------------------------------------------------
        // Clear requests & reponses so each request-response has a clean slate.
        // This avoids request/response object lifecycle leakage across tests.
        //-------------------------------------------------------------------------------------
        $globalRequest = request();
        $globalResponse = response();

        // Reset auth state after every request
        $globalRequest->reset();

        $globalResponse->reset();

        // Reset auth state after request to avoid test leakage
        $this->authenticatedUser = null;
        //-------------------------------------------------------------------------------------

        // wrap response (in TestResponse) and return it
        return new TestResponse($response);
    }



    //--------------------------------------------
    //  Convenience helpers (e.g. get, post, json)
    /*
        You would be able to use the helpers below to:
            $this->get('/home');
            $this->post('/login', ['email' => 'a@b.com']);
            $this->json('POST', '/api/orders', ['qty' => 2]);

        All with:
            ✔ Full routing
            ✔ Full middleware
            ✔ Full controller execution
            ✔ Zero real HTTP calls
    */
    //--------------------------------------------

    protected function get(string $uri, array $headers = []): TestResponse
    {
        return $this->http('GET', $uri, [], $headers);
    }

    protected function post(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->http('POST', $uri, $data, $headers);
    }


    /**
     * Send a JSON request to the app.
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE, etc.)
     * @param string $uri
     * @param array $data Request payload
     * @param array $headers user overrides, used here as additional headers to merge
     * @return TestResponse the returned object, always. You can chain assertJson(), assertStatus(), assertSee()
     */
    protected function json(
        string $method,
        string $uri,
        array $data = [],
        array $headers = []
    ): TestResponse
    {
        // Merge default headers + force JSON-specific headers
        $jsonHeaders = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        $headers = array_merge($this->defaultHeaders ?? [], $jsonHeaders, $headers);

        // Encode data as JSON
        $body = json_encode($data);

        // Prepare server and input
        $this->prepareServerVariables($method, $uri, $headers);

        // For JSON requests, your HttpKernel (or testing dispatch) should read from php://input
        $this->prepareInputStream($body);

        // The DGZ_Request singleton may already be cached with a stale rawInput.
        // Now that TestInputStream is live, force-refresh rawInput on the cached instance.
        $cachedRequest = container(\Dorguzen\Core\DGZ_Request::class);
        $ref = new \ReflectionProperty($cachedRequest, 'rawInput');
        $ref->setAccessible(true);
        $ref->setValue($cachedRequest, file_get_contents('php://input'));

        // Dispatch request via HttpKernel
        $kernel = new HttpKernel();
        $response = $kernel->dispatchForTesting();

        //-------------------------------------------------------------------------------------
        // Clear requests & reponses so each request-response has a clean slate.
        // This avoids request/response object lifecycle leakage across tests.
        //-------------------------------------------------------------------------------------
        $globalRequest = request();
        $globalResponse = response();

        // Reset auth state after every request
        $globalRequest->reset();

        $globalResponse->reset();
        //-------------------------------------------------------------------------------------

        // After dispatching the request, you must restore the php stream wrapper
        // (the native 'php://' streams) or the next test will behave unpredictably.
        // Without this, later calls to php://input will still be hijacked.
        // This basically prevents cross-test contamination.
        stream_wrapper_restore('php');
        TestInputStream::reset();

        return new TestResponse($response);
    }


    //--------------------------------------------
    //  Server environment preparation (critical)
    //--------------------------------------------

    /**
     * ✔ Keeps router happy
     * ✔ Matches how PHP-FPM populates globals
     */
    protected function prepareServerVariables(
        string $method,
        string $uri,
        array $headers
    ): void
    {
        $_SERVER = [];

        $_SERVER['REQUEST_METHOD'] = strtoupper($method);
        $_SERVER['REQUEST_URI']    = $uri;
        $_SERVER['SERVER_NAME']    = 'localhost';
        $_SERVER['HTTP_HOST']      = 'localhost';

        foreach ($headers as $key => $value) {
            $_SERVER[$key] = $value;
        }
    }


    /**
     * Mock php://input for JSON requests
     * In real PHP HTTP requests:
     *  $_POST is populated only for:
     *
     *      -application/x-www-form-urlencoded
     *      -multipart/form-data
     *
     * JSON requests do NOT populate $_POST
     * Instead, frameworks read JSON from:
     *
     *      php://input
     *
     * So in tests, if you want this to work:
     *
     *      $this->postJson('/api', ['name' => 'Gustav']);
     *
     * The framework must be able to read raw JSON input, just like production.
     * That is exactly what prepareInputStream() simulates.
     */
    protected function prepareInputStream(string $content): void
    {
        // Remove any existing input stream
        stream_wrapper_unregister('php');

        // Register our mock stream
        stream_wrapper_register('php', TestInputStream::class);

        // Set content
        TestInputStream::setContent($content);
    }


    //--------------------------------------------
    //  Request data setup
    //--------------------------------------------

    protected function prepareRequestData(string $method, array $data): void
    {
        $_GET = [];
        $_POST = [];
        $_REQUEST = [];

        if ($method === 'GET') {
            $_GET = $data;
        } else {
            $_POST = $data;
        }

        $_REQUEST = array_merge($_GET, $_POST);
    }



    //--------------------------------------------
    //  JSON input stream handling (important)
    //--------------------------------------------

    /**
    * PHP normally reads JSON from php://input, which is read-only.
    * So while the prod env reads from php://input,
    * the testing env reads from TestInputStream
    */
    protected function setJsonInputStream(array $data): void
    {
        TestInputStream::setContent(json_encode($data));
    }



    //--------------------------------------------
    //  Convenience helper methods
    /*
        This makes tests super readable e.g.

            $this->postJson('/api-v1/users', [
                'name' => 'Gustav',
                'email' => 'gustav@dorguzen.com'
            ])->assertStatus(201)
            ->assertJson(['name' => 'Gustav']);
    */
    //--------------------------------------------
    public function getJson(string $uri, array $headers = []): TestResponse
    {
        return $this->json('GET', $uri, [], $headers);
    }

    public function postJson(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->json('POST', $uri, $data, $headers);
    }

    public function putJson(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->json('PUT', $uri, $data, $headers);
    }

    public function deleteJson(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->json('DELETE', $uri, $data, $headers);
    }
}
