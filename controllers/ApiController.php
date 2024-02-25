<?php

namespace controllers;

use DGZ_library\DGZ_Validate;
use DGZ_library\DGZ_Translator;
use ReflectionClass;
use ReflectionException;
use controllers\AuthController;
use configs\Config;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use DomainException;
use InvalidArgumentException;
use UnexpectedValueException;


class ApiController extends \DGZ_library\DGZ_Controller
{

    protected $validatedToken = false;

    protected $response = [
        'status' => '',
        'message' => ''
    ];

    public function __construct()
    {
        parent::__construct();
    }


    public function getDefaultAction()
    {
        return 'defaultAction';
    }


    public function defaultAction()
    {

    }




    public function api($targetController, $targetMethod)
    {
        //-------------------VALIDATE JWT TOKEN ----------------//
        $isValidToken = $this->validateToken();

        if ($this->validatedToken == true)
        {
            //Proceed to handle API request here
            //POST
            if ($_SERVER['REQUEST_METHOD'] == 'POST') 
            {
                //REQUESTS USING THIS
                /*
                * -login
                * -password reset
                */

                $data = file_get_contents('php://input');
                $decoded_json_data = json_decode($data);
                $array_from_object = get_object_vars($decoded_json_data);
                //inject the data into POST for forward handling
                $_POST = $array_from_object;

                $this->setHeaders();
                $controllerName = ucfirst($targetController).'Controller';

                $classPath = '\controllers\\%s';
                $controller = sprintf(
                    $classPath,
                    $controllerName
                );

                $object = new $controller();
                $response = $object->$targetMethod(); 
                if ($response['status'] === true || $response['status'] == 200) {
                    http_response_code(200);
                    $response['response_code'] = 200;
                }
                else if ($response['status'] === false) {
                    http_response_code(400);
                    $response['response_code'] = 400;
                }

                die(json_encode($response));
            }

            //GET
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                //REQUESTS USING THIS
                /*
                * -logout
                */

                $data = file_get_contents('php://input');
                $decoded_json_data = json_decode($data);
                $array_from_object = get_object_vars($decoded_json_data);
                //inject the data into POST for forward handling
                $_GET = $array_from_object;

                $this->setHeaders();
                $controllerName = ucfirst($targetController).'Controller';

                $classPath = '\controllers\\%s';
                $controller = sprintf(
                    $classPath,
                    $controllerName
                );

                $object = new $controller();
                $response = $object->$targetMethod();
                if ($response['status'] === true || $response['status'] == 200) {
                    http_response_code(200);
                    $response['response_code'] = 200;
                }
                else if ($response['status'] === false) {
                    http_response_code(400);
                    $response['response_code'] = 400;
                }

                die(json_encode($response));
            }

            //PATCH
            if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
                $this->setHeaders();

            }

            //DELETE
            if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                $this->setHeaders();
            }
        }
        else
        { 
            //JWT signature validation failed
            $this->setHeaders(["WWW-Authenticate" => "Bearer error='failed_signature_validation'"]);
            http_response_code(401);
            $this->response['status'] = 'false';
            $this->response['response_code'] = 401;
            $this->response['message'] = 'Authorization on signature failed';
            $this->validatedToken = false;
            die(json_encode($this->response));
        }
    }





    //pass it an associative array of key-value pairs eg ["Content-Type" => "application/json"]
    public function setHeaders($headers = [])
    {
        // start output buffering
        ob_start(); 
        header("Access-Control-Allow-Origin: ".$this->config->getHomePage());
        //allow cookies to be used in the communication (once this is used, the 
        //'...Allow-Origin...' header above will no longer work with a wildcard
        //-u would have to explicitly spec the domain where these cookies are 
        //allowed to be used in. This is coz domains are not normally allowed to
        //be used across multiple platforms)
        //In the above case, instead of a wildcard (*) we have specified the homepage
        //URL as the place where this app is setting & reading from cookies, 
        //particularly in managing whether a user wants to be remembered after
        //authentication.
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE");
        header("Access-Control-Max-Age: 3600");
        header("Content-Type: application/json; charset=UTF-8");

        //Override headers 
        // send headers
        if (!empty($headers))
        {
            foreach ($headers as $key => $value) {
                header("$key: $value"); // e.g. "Content-Type: application/json";
            }
        }

        // Flush the buffer, sending the content to the client
        ob_end_flush();
    }




    public function getServerVariable($variableName)
    {
        $client_request_headers = getallheaders();
        return $client_request_headers[$variableName] ?? false;
    }





    public function validateToken()
    {
        $config = new Config();
        $jwt_secret = $config->getConfig()['jwt-secret-key'];
        $response = [
            'status' => '',
            'message' => ''
        ];
        //prepare claims for the JWT
        $issuedAt = time();
        $payload = [
            "iss" => "https://yourAppname-api.net",
            "aud" => "https://yourAppname-api.com",
            //"iat" => $issuedAt,
            //"nbf" => $issuedAt,
            "data" => [
                    "username" => "gustavfn",
                    "plan" => "gustavfn",
            ]
        ];

        $jwt = JWT::encode($payload, $jwt_secret, 'HS256');
        //this $jwt is the very long string
        //Verify the request JWT token
        //First, is there an AUTHORIZATION header?
        $authHeader = $this->getServerVariable('Authorization');
        
        if (!$authHeader)
        {
            //return failed response
            $response['status'] = false;    
            http_response_code(401);
            $response['response_code'] = 401;
            $response['message'] = "Bearer error='invalid_token'";
            $this->setHeaders(["WWW-Authenticate" => "Bearer error='missing_token'"]);
            $this->validatedToken = false;
            return $response; 
        }
        //If there is an Authorization header, validate it
        //Isolate the token ie remove 'Bearer ' from the value of $authHeader 
        // (eg Bearer sfsffgdgddsghdsghdssdsdgdshahahaha)   
        $token = preg_replace('/^Bearer\s*/', '', $authHeader);

        //try to decode the JWT token
        try { 
            $decoded = JWT::decode($jwt, new Key($jwt_secret, 'HS256'));

            /*
            NOTE: This will now be an object instead of an associative array. To get
            an associative array, you will need to cast it as such:
            */
            $decoded_array = (array) $decoded;

            //Here we should have a decoded token
            //The token is valid. Proceed with API call & then do what you wanna do 
            //with claims before passing your response back to the client 
            $this->validatedToken = true;
        }
        //catch whatever exceptions you wanna handle individually
        catch (InvalidArgumentException $e)
        {
            //the provided key/key-array is empty or malformed
            $response['status'] = false;    
            http_response_code(401);
            $response['response_code'] = 401;
            $response['message'] = $e->getMessage();
            $this->setHeaders(["WWW-Authenticate" => "Bearer error='failed_signature_validation'"]);
            $this->validatedToken = false;
            return $response;
        }
        catch (DomainException $e)
        {
            //the provided algorithm is unsupported or...
            //the provided key is invalid or...
            //unknown error thrown in openssl or libsodium or...
            //libsodium is required but not available...
            $response['status'] = false;    
            http_response_code(401);
            $response['response_code'] = 401;
            $response['message'] = $e->getMessage();
            $this->setHeaders(["WWW-Authenticate" => "Bearer error='failed_signature_validation'"]);
            $this->validatedToken = false;
            return $response;
        }
        catch (SignatureInvalidException $e)
        {
            //the provided JWT signature validation failed
            $response['status'] = false;    
            http_response_code(401);
            $response['response_code'] = 401;
            $response['message'] = "Authorization on signature failed";
            $this->setHeaders(["WWW-Authenticate" => "Bearer error='failed_signature_validation'"]);
            $this->validatedToken = false;
            return $response;
        }
        catch (BeforeValidException $e)
        {
            //the provided JWT is trying to be used before "nbf" claim or...
            //the provided JWT is trying to be used before "iat" claim
            $response['status'] = false;    
            http_response_code(401);
            $response['response_code'] = 401;
            $response['message'] = $e->getMessage();
            $this->setHeaders(["WWW-Authenticate" => "Bearer error='failed_signature_validation'"]);
            $this->validatedToken = false;
            return $response;
        }
        catch (ExpiredException $e)
        {
            //the provided JWT is trying to be used after "exp" claim
            $response['status'] = false;    
            http_response_code(401);
            $response['response_code'] = 401;
            $response['message'] = "Auth token has expired";
            $this->setHeaders(["WWW-Authenticate" => "Bearer error='expired_token'"]);
            $this->validatedToken = false;
            return $response; 
        }
        catch (UnexpectedValueException $e)
        {
            //the provided JWT is malformed or...
            //the provided JWT is missing an algorithm/using an unsupported algorithm or...
            //the provided JWT algorithm does not match provided key or...
            //the provided key ID in key/key-array is empty or invalid
            $response['status'] = false;    
            http_response_code(401);
            $response['response_code'] = 401;
            $response['message'] = "Auth token has invalid";
            $this->setHeaders(["WWW-Authenticate" => "Bearer error='invalid_token'"]);
            $this->validatedToken = false;
            return $response; 
        }
    }






    /**
     * DOCUMENTATION (see the file 'apiDocs.txt')
     * 
     * -The entry point of this API via this class is:
     * 
     *      http://Dorguzen/api/className/methodName
     * 
     *      Replace the 'Dorguzen' with your app name, & 'className' & 'methodName' with the request 
     *      target classname & methodName (optional), respectively.
     * 
     * 
     * 
     * HOW JWT (Json Web Tokens) WORKS
     * -------------------------------
     * -The client sends a POST request to the server's authentication endpoint,
     *  including the users' credentials in the body of the request.
     * -The server validates the credentials in the request body, & generates a JWT 
     *  if they are correct, signs it, & sends it back to the client.
     * - The client stores the JWT & includes it in the Authorization header
     *  in subsequent HTTP requests in order to access protected resources.
     * 
     * -You can generate your secret key using a tool like openssl
     * -You can test your parameters and their validation on jwt.io
    
     * -A JWT is made up of 3 parts;
     *      i)-Headers
     *         -It may look like so:
     *          {
     *               "alg": "HS256",
     *               "typ": "JWT"
     *           }
     * 
     *          where 'alg' is the encryption method, of which HS256 is a very popular one
     *          'typ' refers to the type of token, which in this case is a json web token (JWT)
     * 
     *      ii)-Payload
     *          -It may look like so:
     * 
     *              {
     *                   "iss": "https://yourAppname-api.net",
     *                   "aud": "https://yourAppname-api.com",
     *                   "iat": "1698929050",
     *                   "nbf": "1698929050",
     *                   "sub": "1234567890",
     *                   "name": "John Doe",
     *                   "iat": 1516239022,
     *                   "data": {
     *                          "username": "gustavfn",
     *                          "plan": "gustavfn",
     *                   }
     *               }
     *          -The payload contains what are known as claims. 
     *          -Registered claims are info about the user & additional metadata like the issuing 
     *              server, the expiry date/time (of the token) and the request. These are called 
     *              registered claims because they are standard & carry the same meaning in all JWTs.
     *          -You can also have private claims which come in the 'data' object. These are custom 
     *              information eg username, plan, permissions etc of the user to which the JWT belongs.
     *              This private claims object makes JWT so powerful in that instead of using sessions, 
     *              you can as well store information about the user here, as JWTs are very secure too.
     *              What makes this powerful is that because you store the user's info in a JWT, you can 
     *              have this user access multiple applications on different servers using the same JWT,
     *              something that would have otherwise been only possible by storing user session data 
     *              in a shared DB, & having to read from it across the different servers, which will be
     *              relatively slower, especially if you have many requests to be making to that DB. 
     *          -iss means Issuing server
     *          -aud means audience-how this resource is being publicly accessed
     *          -iat means 'issued at' & its value is a Unix timestamp of the date/time when the token 
     *              was issued
     *          -nbf means 'not before' & it's also a Unix timestamp of when not to verify the JWT before
     * 
     *      iii)-Signature
     *             -It may look like so:
     *                  HMACSHA256(
     *                       base64UrlEncode(header) + "." +
     *                       base64UrlEncode(payload),
     *                       
     *                       your-256-bit-secret
     *   
     *                  ) secret base64 encoded
     * 
     *           -All this information in the signature is encrypted but you can view its contents on
     *              'https://jwt.io'
     *           -The bit that says 'your-256-bit-secret' is your actual JWT string which you can paste 
     *              in here & see the encoded result on the left pane on 'https://jwt.io'
     *              This signature section of the JWT consists an encrypted (in your chosen algorythm eg 'HS256')
     *              of the header, the payload, and the secret string that you provided (which you can generate 
     *              using a tool like openssl for & store it in a config file in your application).  
     *          -When this signature is decoded and it matches the JWT string provided in the API request by 
     *              a user, then the JWT will be deemed as valid.
     */

     /**
      * STEPS TO IMPLEMENT JWT IN YOUR API
      * --------------------------------
      * -Get Firebase JWT library
      *      -There are other libraries you can use as well, & these are all listed on 
      *          the packagist website. However, it's good to go for one that conforms to 
      *          the RFC 7519 standard, and firebase/jwt does.
      *      -for the docs & how to install, visit: 'https://github.com/firebase/php-jwt'
      *      -Install it like so in your app root directory: composer require firebase/php-jwt
      * 
      * -Then its time to implement the JWT. First you need to generate a secret key. Just vas a tip, you can 
      *     auto-generate one in your terminal using openssl (package) liike so:

      *     openssl rand -base64 32

      *      The -base64 & 32 arguments just make sure you get a 256 bit secret key generated. 32 is the number 
      *          of random bytes to be generated (8 bits = 1 byte, so 32 bytes equals 256 bits)

      * -Obtain credentials from the user request which are needed for the specific API 
      *     request eg username & password for a login request and handle them as you would normally do
      * -Encode the JWT
      *      -json encode the payload, and the base64urlencoded header + the payload, & sign using the secret key
      * -Return the JWT code to the cient, or exit if the JWT is not valid
      * -Next, when the user makes a request to the API, check the user submitted data for this JWT token which 
      *      all requests MUST contain. Check the JWT token like so:
      *          The long token string that the user must send thriugh can be generated by you (and given to them)
      *          usingb the decode() method of JWT class like so:

      *          $jwt = JWT::encode($payload, $jwt_secret, 'HS256');

      *          -examine the Authorization header of the user request
      *          -perform a verification using the JWT library
      *          -If the JWT token is valid, progress the request, if not, return an authentication failure 
      *              response with a 401 code back to the user.
      *
      *     -The way a user sends the bearer (JWT) token with a request is via a header param called 
      *         HTTP_AUTHORIZATION. To do so in postman eg, 
      
      *         -Go to new request
      *         -In the Authorization tab, in the Type dropdown, select 'Bearer Token' 
      *         -On the right pane, in the 'Token' field, paste the token string in there & this will come through
      *             to the server as the value of the 'HTTP_AUTHORIZATION' key. The value will be something like 
      *             'Bearer header.payload.signature'. To verify the request token, we will need to split this 
      *             value of the Authorization header by first; removing the 'Bearer ' string at the start of its
      *             value, then splitting the rest of it to isolate the three parts of the 
      *             -Next we will verify it simply by decoding it with the JWT library's decode() method.

      *     THINGS TO FIGURE OUT
      *     --------------------
      *     WHAT ABOUT THE EXPIRY OF THE TOKEN?
      *     What about renewing of it, can that happen dynamically?
      *     How do you write/save data to the JWT payload for subsequest use?


      * PRECAUTIONS IN IMPLEMENTING JWT
      * --------------------------------
      * -Use HTTPS
      * -Keep it secret, keep it safe
      * -Strong secret key (use 256 bit, randomly generated)
      * -Ensure token expiration times. Make it short eg an hour
      * -Handle token refresh safely
      * - Validate user input
      * -Avoid very sensitive information being passed through
      * -Implement proper error handling (use in-built jwt exceptions)
      * -Regularly update your libraries 
      */

















}