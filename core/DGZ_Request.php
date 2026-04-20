<?php

namespace Dorguzen\Core;


class DGZ_Request
{
    protected array $get;
    protected array $post;
    protected array $files;
    protected array $server;
    protected array $cookies;
    protected ?array $json = null;
    protected array $headers;
    protected string $rawInput;

    /* used to hold any detail item id from request parameters */
    protected string $targetId;

    /** @var $user placeholder in case you ever want to pass a user object around */
    protected ?object $user;

    public function __construct()
    {
        $this->get = $_GET ?? [];
        $this->post = $_POST ?? [];
        $this->files = $_FILES ?? [];
        $this->server = $_SERVER ?? [];
        $this->cookies = $_COOKIE ?? [];

        // Attempt to parse JSON payload if available
        $input = file_get_contents('php://input');
        if ($this->isJson($input)) {
            $this->json = json_decode($input, true);
        }
        
        // this captures the raw body of API requests (like JSON or XML).
        $this->rawInput = file_get_contents('php://input') ?: '';
    }


    //------------------------------------------------------------------
    //                          ACCESSORS
    //------------------------------------------------------------------
    public function get(string $key = '', $default = null)
    {
        return $key ? ($this->get[$key] ?? $default) : $this->get;
    }


    public function post(string $key = '', $default = null)
    {
        return $key ? ($this->post[$key] ?? $default) : $this->post;
    }


    public function file(string $key = '')
    {
        return $key ? ($this->files[$key] ?? null) : $this->files;
    }


    public function header(string $name = '')
    {
        $headers = $this->getHeaders();
        return $name ? ($headers[$name] ?? null) : $headers;
    }


    public function json(string $key = '', $default = null)
    {
        return $key ? ($this->json[$key] ?? $default) : $this->json;
    }

    public function setTargetId($targetId)
    {
        $this->targetId = $targetId;
    }

    public function getTargetId()
    {
        return $this->targetId;
    }


    /**
     * setUser() is used to temporarily attach an authenticated user to the request.
     * Used mainly for testing or programmatic authentication. Can be used to simulate 
     * an authenticated user too.
     * @param object $user
     * @return void
     */
    public function setUser(?object $user)
    {
        $this->user = $user;
    }


    public function reset(): void
    {
        $this->user = null;
        $this->json = null;
        $this->rawInput = '';
    }


    /**
     * user() can be used to fetch the currently authenticated user on the global request 
     * if it was set. Example use:
     * 
     *  $user = request()->user();
     * 
     * @return object|null
     */
    public function user(): mixed
    {
        return $this->user ?? null;
    }

    //------------------------------------------------------------------



    //------------------------------------------------------------------
    //                              HELPERS
    //------------------------------------------------------------------
    /**
     * This method will correctly detect and handle all HTTP methods, including:
     *  GET
     *  POST
     *  PUT
     *  PATCH
     *  DELETE
     *  and even others like OPTIONS or HEAD.
     *  All these methods will be handled out of the box from APIs or JS fetch calls,
     *  However, there is an issue when it comes to some of these HTTP header method
     *  types when dealing with HTML forms.
     * 
     * ⚠️ Note for browsers and HTML forms
     *  
     *  Standard HTML forms (<form>) only support GET and POST.
     *  So if you want to simulate PUT, PATCH, or DELETE from a form, you'll need this small trick:
     *      -Add a hidden form input field with a name of '_method' and a value of the desired 
     *          method eg 'DLETE'
     *  
     *          <form action="/users/1" method="POST">
     *            <input type="hidden" name="_csrf_token" value="<?=getCsrfToken()?>">
     *            <input type="hidden" name="_method" value="DELETE">  
     *            <button type="submit">Delete</button>
     *          </form>
     * 
     *      -This method will detect that and handle the request as per the method name submitted via that 
     *          form's hidden field.
     *
     * @return string
     */
    public function method(): string
    {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');

        // Allow method override (for forms)
        if ($method === 'POST' && isset($this->post['_method'])) {
            $method = strtoupper($this->post['_method']);
        }

        return $method;
    }

   
    public function uri(): string
    {
        return strtok($this->server['REQUEST_URI'] ?? '/', '?');
    }


    public function isJsonRequest(): bool
    {
        $contentType = $this->server['CONTENT_TYPE'] ?? '';
        return str_contains($contentType, 'application/json');
    }


    /**
     * retrieve JSON (API) request data
     */
    public function getJson(): array
    {
        $data = json_decode($this->rawInput, true);
        return is_array($data) ? $data : [];
    }


    // get the IP address of a client
    public function getClientIp(): ?string
    {
        $keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($keys as $key) {
            if (!empty($this->server[$key])) {
                // X_FORWARDED_FOR can contain multiple IPs: "client, proxy1, proxy2"
                $ipList = explode(',', $this->server[$key]);
                foreach ($ipList as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        return $ip;
                    }
                }
            }
        }

        return null;
    }


    /**
     * Retrieves the Authorization header and extracts a Bearer token (for APIs using JWTs or OAuth2).
     */
    public function getBearerToken(): ?string
    {
        $header = $this->header('AUTHORIZATION') ?? $this->header('Authorization');

        if ($header && preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }


    /**
     * Detects if a request was made via JavaScript (XMLHttpRequest or fetch())
     * @return bool
     */
    public function isAjax(): bool
    {
        return (
            isset($this->server['HTTP_X_REQUESTED_WITH']) &&
            strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        );
    }
    //------------------------------------------------------------------



    //------------------------------------------------------------------
    //                    CSRF PROTECTION HELPERS
    //------------------------------------------------------------------
    /**
     * Generate and store a new CSRF token in the session.
     */
    public function createCsrfToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }


    /**
     * Get the current CSRF token for this session (or generate if missing).
     *  -bin2hex(random_bytes(32)) = strong, cryptographically secure token.
     *  -hash_equals() = prevents timing attacks.
     */
    public function getCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            return $this->createCsrfToken();
        }
        return $_SESSION['csrf_token'];
    }


    /**
     * Validate a user-submitted CSRF token (e.g., from POST or request headers).
     */
    public function validateCsrfToken(?string $token): bool
    {
        $stored = $_SESSION['csrf_token'] ?? '';
        return $stored && hash_equals($stored, (string)$token);
    }


    /**
     * Automatically retrieves the CSRF token from POST, request headers, or JSON body.
     * It can be used on all of these requests:
     *      -Regular HTML forms (can be sent via a hidden field named '_csrf_token')
     *      -AJAX requests (using X-CSRF-TOKEN header)
     */
    public function getCsrfTokenFromRequest(): ?string
    {
        // 1. Check common locations. We try POST/GET first
        if (isset($this->post['_csrf_token'])) {
            return $this->post['_csrf_token'];
        }

        if (isset($this->get['_csrf_token'])) {
            return $this->get['_csrf_token'];
        }

        // 2. Try JSON data submissions
        $json = $this->getJson();
        if (isset($json['_csrf_token'])) {
            return $json['_csrf_token'];
        }

        // 3. Then try headers
        $headerToken = $this->header('X-CSRF-TOKEN');
        if ($headerToken) {
            return $headerToken;
        }

        // Or try lowercase headers (some servers lowercase headers)
        $lowercaseHeaderToken = $this->header('x-csrf-token');
        if ($lowercaseHeaderToken) {
            return $lowercaseHeaderToken;
        }

        return null;
    }

    //--------------------------------------------- --------------------



    //------------------------------------------------------------------
    //                    INTERNAL HELPERS
    //------------------------------------------------------------------
    protected function getHeaders(): array
    {
        $headers = [];
        foreach ($this->server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', substr($key, 5));
                $headers[$name] = $value;
            }
        }
        return $headers;
    }


    protected function isJson(string $string): bool
    {
        if (empty($string)) return false;
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    //------------------------------------------------------------------
}

