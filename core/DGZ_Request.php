<?php

namespace Dorguzen\Core;

/*
//--------------------------------------------------------------------------------
        DOCS
//--------------------------------------------------------------------------------
    Example usage of this Request object
    -----------------------------
    -In controllers/services, get submitted values from requests like this:
     //-----------------------------------------------
        $username = $this->request->post('username');
        $email = $this->request->post('email');
        $file = $this->request->file('avatar');


    -Retrieve files after they have been uploaded
     //-----------------------------------------------
        $file = $request->file('avatar');

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $tmpName = $file['tmp_name'];
            $name = basename($file['name']);
            move_uploaded_file($tmpName, __DIR__ . "/uploads/$name");
        }


    -Get values from GET requests like this:
     //-----------------------------------------------

        $page = $this->request->get('page', 1);
        $sort = $this->request->get('sort', 'latest');

    -Handle and retrieve values from API JSON requests like this:
     //-----------------------------------------------
        $userId = $this->request->json('user_id');
        $productId = $this->request->json('product_id');

        if (!$userId || !$productId) {
            return $this->response->setData([
                'error' => 'Missing required fields'
            ])->setStatus(400)->send();
        }

        // proceed...

        OR

        $data = json_decode($request->getRawInput(), true);
        echo $data['email'];
        echo $data['password'];

        OR (even easier)

        $email = $request->getJson()['email'] ?? null;


    -To detect a request type or method:
     //-----------------------------------------------
        if ($this->request->method() === 'POST') {
            // handle form or API submission
        }

        if ($this->request->isJsonRequest()) {
            // handle JSON body
        }

    -How DGZ_Request automatically capture data from the $_REQUEST super global
     //-----------------------------------------------
      This DGZ_Request object already comes pre-populated with values from 
      PHP's superglobals, and persists them, ready for you to use where you please.
      This block of code below does the exact same thing that many PHP frameworks do
      with a function like Request::createFromGlobals() to capture the values of 
      super globals and load it into its properties, ready for you to use. We do not 
      need that here, and this code block in the constructor of DGZ_Request already 
      does exactly that:

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

      This is great, firstly, because this code is in the contstructor, it will 
      capture and load all the super global data REQUEST data in itself (properties), 
      ready for you to use.

      Secondly, the other thing great DGZ does is; since it is binding it in bootstrap.php file 
      like this:

        $container->set(DGZ_Request::class, function() {
            ... 
            new DGZ_Request()
        }

      it means every controller and service that type-hints DGZ_Request will automatically get 
      the same fully populated instance for that request.
      You don’t need to do anything extra.

    -Using a class as a singleton
     //-----------------------------------------------
     Let's use the example of how we instantiate the
     DGZ_Request class in bootstrap.php to make it globally accessible in the application.
     The class could have been instantiated in any of the following ways:

        -a) Instantiating with a static caching mechanism

            $container->set(DGZ_Request::class, function() {
                static $request;
                if (!$request) {
                    $request = new DGZ_Request();
                }
                return $request;
            });

            OR
        -b) Instantiating with an arrow function (short-hand)

            $container->set(DGZ_Request::class, fn() => new DGZ_Request());

     Both approaches will work. The difference is that the former version adds a static 
     caching mechanism so that:
     
        -the first time the container asks for DGZ_Request, a new one is created.
        -Then every subsequent call will return the same instance (singleton-style behavior).

     Whereas the fn() => new DGZ_Request() version creates a fresh instance each time.
     What is fn()? fn() is PHP’s arrow function syntax, introduced in PHP 7.4.
     It’s a short-hand for creating anonymous functions (closures) that return a single 
     expression. It automatically inherits variables from the parent scope (no need for use(...)).
        Which one is better? The former version (with static) is better in the context of the
     bootstrap.php global configuration file. This is because the HTTP request never changes 
     mid-lifecycle — there’s always one request per application run. So, caching it this way 
     results in the same DGZ_Request instance being reused across controllers, 
     models, etc. This consumes less memory, and there is no redundant parsing.


     -Get the IP address of a client
      //-----------------------------------------------
        $ip = $request->getClientIp();


    -Retrieve the Authorization header and extracts a Bearer token 
     //-----------------------------------------------
     This will be very useful for APIs using JWTs or OAuth2. Here is an example:

        $token = $request->getBearerToken();
        if ($token) {
            // validate JWT, for example
        }


    -Detect if a request is an AJAX request
     //-----------------------------------------------
        if ($request->isAjax()) {
            // Return JSON instead of rendering HTML
        }


    -How the CSRF is implemented in DGZ
     //-----------------------------------------------
     It is done by creating an encrypted token string which is tored in the session
     For it to work, a session is started, ideally in the bootstrap of the app. Here is 
     some more information about how it all works.
        A CSRF token should ideally be created for every user at login and stored in the session just 
     like all the other session variables we store for them (e.g., user_id, email, etc.). We can then 
     delete them when they sign out. To do so, simply call the request->getCsrfToken(). It gets the 
     CSRF token in the user's session if it exists, or creates one and returns its value.  

        -The following four helper methods are created in the request object 
            (Dorguzen\Core\DGZ_Request):

                -createCsrfToken()
                -getCsrfToken()
                -validateCsrfToken(?string $token)
                -getCsrfTokenFromRequest() 

        -This is how to check for a CSRF token & validate it during user requests

            -Form submission

                <form method="POST" action="/account/updateAccount">
                    <input type="hidden" name="_csrf_token" value="<?= $request->getCsrfToken() ?>">
                    <input type="text" name="email">
                    <button type="submit">Save</button>
                </form>

                Notice how the value of the 'csrf_token' field is the result of calling the DGZ global 
                helper function $request->getCsrfToken(). Just getCsrfToken() will still work.

                Note that $request->createCsrfToken() or just createCsrfToken() will work too, as they
                all create a new token if one does not exist, before sending back the token.

                If you sent the request as AJAX, just ensure that you are also generating a token and 
                sending that via the 'X-CSRF-TOKEN' header. Here's an example: 

                    fetch('/user/update-profile', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': generatedCsrfToken
                        },
                        body: JSON.stringify({ name: 'Gustav' })
                    });

                Keep in mind that creating a CSRF to use in your app is flexible, either you choose to 
                generate one and store in a session each time a user logs in, or you can just create 
                one on demand (using createCsrfToken() or getCsrfToken()) whenever you want to use in 
                a form. This means that you can use it to submit forms even when the user is not 
                authenticated. 

            -Handle the form submission in your controller. Notice that the getCsrfTokenFromRequest()
                which is how we grab the CSRF token from the form, is checking for the submitted form 
                field by the name/key of '_csrf_token' both for POST as well as for JSON API requests 
                like so:
                    if (isset($this->post['_csrf_token'])) {
                        ...
                    }

                    $json = $this->getJson();
                    if (isset($json['_csrf_token'])) { ... }
            
                So, after grabbing the user-submitted token (using getCsrfTokenFromRequest()),
                it is validated against what is in the session (using validateCsrfToken($token)).
                Return an error response if it fails validation, or proceed with the request as 
                normal, if ok. 

                    public function updateAccount()
                    {
                        // grab the user-submitted token
                        $token = $this->request->getCsrfTokenFromRequest();

                        // verify the token
                        if (!$this->request->validateCsrfToken($token)) {
                            return $this->response->json(['error' => 'Invalid CSRF token'], 403);
                        }

                        // Proceed if valid
                        $email = $this->request->post('email');
                        // ...
                    }


            -Optionally, you can move the check to your middleware and have the token check & validation
             run for all sensitive requests like: POST, PUT, DELETE, & return an error response if it 
             fails. This is better and saves you having to do CSRF validation in all your controlers.
             DGZ has already ccomplished that for you in the following 2 steps:

                -a) A method; checkCsrfProtection() was created in the Middleware class (in middleware/Middleware.php).
                    The contents of the method are as follows:

                        public function checkCsrfProtection(): bool
                        {
                            /** @var DGZ_Request $request */ /*
                            $request = container(DGZ_Request::class);

                            $method = $request->method();
                            $uri = $request->uri();

                            // Get CSRF exceptions from config
                            $csrfExcepts = $this->config->getConfig()['csrf_except'] ?? [];

                            // Only enforce CSRF on unsafe HTTP methods
                            if (in_array($method, ['POST', 'PUT', 'DELETE'])) {

                                // Skip paths that match exceptions
                                foreach ($csrfExcepts as $exceptPath) {
                                    if (stripos($uri, $exceptPath) !== false) {
                                        return true; // CSRF not required here
                                    }
                                }

                                // Retrieve token using DGZ_Request’s helper
                                $token = $request->getCsrfTokenFromRequest();

                                if (!$request->validateCsrfToken($token)) {
                                    return false;
                                }
                            }

                            return true;
                        }

                -b) Next, in the DGZ_Router class, when the request controller & method are identified, 
                    in the middleware section, just before executing the controller method, we validate 
                    the request for CSRF attacks like so:

                        $middleware = new Middleware($controller, $method);

                        if ($middleware->checkCsrfProtection() === false)
                        {
                            throw new DGZ_Exception(
                                'Not authorized', 
                                DGZ_Exception::PERMISSION_DENIED, 
                                'Invalid or missing CSRF token. If you submitted a form, make sure the form has a hidden field of
                                the name _csrf_token, and its value the result of calling the global getCsrfToken() or getCsrfToken() 
                                function, or if it was an AJAX request, be sure to send the X-CSRF-TOKEN header with its value as the 
                                generated token from getCsrfToken() or getCsrfToken()'
                            );
                        }

                That's it. Again, this way you don’t even have to manually check for csrf inside controllers.


            -How to use CSRF with AJAX requests
              In your web form, just like with any other form as described earlier above, you would 
              have a field like this:

                <input type="hidden" name="_csrf_token" value="<?= $request->getCsrfToken() ?>">

              Remember the token value is from the server (saved in the sessions), so you would use 
              JavaScript to extract that value and insert it into your AJAX code when preparing to 
              send off the request. It should be passed in the headers, as the value of the 
              'X-CSRF-TOKEN'. Here is the example code:
              
                fetch('/user/update-profile', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': generatedCsrfToken
                    },
                    body: JSON.stringify({ name: 'Gustav' })
                });

              In your controller, you would detect if its an AJAX call as shown above, then handle 
              the request as normal-extract, and validate the token, before proceeding. 


            -A good security practice is to re-generate this session token eg at every login
              as we have discussed above. It doesn't have to be at login, but after logging them 
              sounds like a good time to generate it so it is managed together with al the other 
              session values of the user, and when they logout, the whole session including that 
              CSRF token is cleared in one go.  
              To do so, you would add another helper your Request object which you can call from wherever 
              choose to refresh the value of the session CRSF token.
                Anyway, however you choose to do it, the helper method is there on the request object for 
              you to call to recreate, and, or get the token. 

                public function createCsrfToken(): string
                {
                    $token = bin2hex(random_bytes(32));
                    $_SESSION['csrf_token'] = $token;
                    return $token;
                }

              Here is an example of doing that after login the user in, from your controller: 

                public function doLogin()
                {
                    // ... validate credentials then 
                    // create their session vars when they're successfully authenticated.
                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['user_email'] = $user->email;

                    // ✅ Create a CSRF token for this new session
                    $csrfToken = $this->request->createCsrfToken();
                    $_SESSION['csrf_token'] = $csrfToken;

                    // Optionally, send it in response for SPA / API apps
                    return $this->response->json([
                        'message' => 'Login successful',
                        'csrf_token' => $csrfToken,
                    ]);
                }


            -Should i use CSRF tokens with API requests? The answer is NO. Here is
             why.

                1. CSRF tokens — for stateful (session-based) web apps       
                    CSRF exists only because of cookies + sessions.
                    When a browser automatically sends cookies with every request, a malicious site can trick t
                    he user's browser into making unwanted requests.

                    The CSRF token, stored in the user's session and embedded in forms, ensures the request originated 
                    from your own site.

                    So:

                    ✅ Use CSRF for your web application, where sessions and forms exist.
                    It's created once per session (e.g., after login) and destroyed on logout — just as explained earlier.

                2. JWT / e.g. Firebase tokens — for stateless (API) systems
                    APIs don't use browser cookies or sessions; they use headers (Authorization: Bearer <token>).
                    Because the browser doesn't automatically attach JWTs, CSRF isn't an issue here.
                    JWTs already guarantee request authenticity, since each API request must explicitly send a 
                    valid signed token.

                    So:

                    ✅ Use JWTs for API authentication.
                    🚫 No need for CSRF protection in your API routes, because no implicit credentials are sent.

                3. Common Hybrid Strategy (Best Practice)
                    Here's how most secure setups work, and you may have guessed it already:

                    Context	                         Authentication	             CSRF Needed?	    Storage
                    --------------------------------------------------------------------------------------------------------
                    Regular web app (HTML forms)	 Sessions (cookies)	         ✅ Yes	           Session
                    API (mobile apps, SPA, etc.)	 JWT / Firebase token        No                 Local storage or header   
                    
            
            
            -How to disable CSRF validation only for your API routes
                -Add an entry to your config file with route or controller names to skip CSRF validation on e.g. 

                    'csrf_except' => [
                        '/api/',
                    ]

                 This entry can also be in the config file of a specific module, and that will work best especially 
                    if your API exists in DGZ as a separate module. Currently, it's not the case, and all api/ calls 
                    are routed via the ApiController. But the plan is to move that to a module later. When it will 
                    be a module, the config file of the module, just like that of all all modules, will live in:

                        configs/apiModuleConfig.php

                -Next, in the validateCsrfToken($token) method of your DGZ_Request class, you check for this 
                    exception array and ignore the validation if a match is found for the current route eg:

                        public function validateCsrfToken($token) 
                        {
                            // 1️⃣ Skip validation for excluded routes
                            $except = $this->config->getConfig()['csrf_except'];
                            $uri = $_SERVER['REQUEST_URI'] ?? '';

                            foreach ($except as $pattern) {
                                if (str_starts_with($uri, $pattern)) {
                                    return true; // skip validation
                                }
                            }

                            // 2️⃣ Otherwise validate normally
                            $stored = $_SESSION['csrf_token'] ?? '';
                            return $stored && hash_equals($stored, (string)$token);
                        }

                -Alternatively, you can take it a step further and do the check more centrally in your 
                    front controller bootstrap.php, or in your middleware. This will look like the 
                    middleware example given above:

                    if (in_array($request->method(), ['POST', 'PUT', 'DELETE'])) {

                        //do your check to ignore the excluded request types here
                        $except = $this->config->getConfig()['csrf_except'];
                        ...

                        // proceed to validate as normal if current request is not excluded
                        $token = $request->getCsrfTokenFromRequest();
                        if (!$request->validateCsrfToken($token)) {
                            die("Invalid CSRF token");
                        }
                    }


        -TODO: Now i am instantiating DGZ_Request and DGZ_Response classes in the coinstructor of 
            DGZ_Controller, and i am also instantiating it in a singleton fashion in the bootstrap, 
            will that DGZ_Controller instantiation (not a type-hinting) not disrupt the plan of 
            always having only a single instance of DGZ_Request?

           -How many types of super globals are there, 6? have we got them all in DGZ_Request?
           -What's exactly the thing that makes the global helper func work. I guess the fact 
            that they're set in the index.php (front controller), right?

           -Check how to DGZ_Form class handles csrf and make it use the new CSRF feature
        
    
//--------------------------------------------------------------------------------


*/

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

