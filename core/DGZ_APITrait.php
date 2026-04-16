<?php
namespace Dorguzen\Core;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use DomainException;
use InvalidArgumentException;
use UnexpectedValueException;
use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Response;
use Dorguzen\Models\Refresh_tokens;
use Exception;



trait DGZ_APITrait
{

    protected $validatedToken = false;

    protected $validatedUser = null;

    /**
     * You can pass it an associative array of key-value pairs eg ["Content-Type" => "application/json"]
     * and they will be added to the default set of headers being added here.
     * @param mixed $headers
     * @return void
     */
    public function setHeaders($headers = [])
    {
        $config = container(Config::class);
        // start output buffering
        ob_start(); 
        header("Access-Control-Allow-Origin: ".$config->getHomePage());
        //allow cookies to be used in the communication (once this is used, the 
        //'...Allow-Origin...' header above will no longer work with a wildcard
        //-u would have to explicitly specify the domain where these cookies are 
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


    private function generateTokens($userId)
    {
        $config = container(Config::class);
        $jwt_secret = $config->getConfig()['jwt-secret-key'];
        $encoding_algorithm = $config->getConfig()['encoding_algorithm'];
        $appHomePage = $config->getHomePage();

        $issuedAt = time();
        //$accessTokenExp = $issuedAt + 1800;   // 30 minutes
        $accessTokenExp = $issuedAt + 18000;   // 5 hours (for testing)
        //$refreshTokenExp = $issuedAt + 3600; // 1 hour
        $refreshTokenExp = $issuedAt + 7200; // 10 hours (for testing)

        // Access token payload
        $accessPayload = [
            "iss" => $appHomePage,
            "aud" => $appHomePage,
            "iat" => $issuedAt,
            "exp" => $accessTokenExp,
            "data" => [
                "user_id" => $userId
            ]
        ];

        // Refresh token payload (usually minimal, just user id or unique jti)
        $refreshPayload = [
            "iss" => $appHomePage,
            "aud" => $appHomePage,
            "iat" => $issuedAt,
            "exp" => $refreshTokenExp,
            "data" => [
                "user_id" => $userId
            ]
        ];

        $accessToken = JWT::encode($accessPayload, $jwt_secret, $encoding_algorithm);
        $refreshToken = JWT::encode($refreshPayload, $jwt_secret, $encoding_algorithm);

        return [
            'access_token' => $accessToken,
            'access_token_expiry' => $accessTokenExp,
            'refresh_token' => $refreshToken,
            'refresh_token_expiry' => $refreshTokenExp,
        ];
    }



    /**
     * Check if token is valid, & not expired
     *  -1) get the Authorization header 
     *  -2) extract the token from that header (Bearer <token>)
     *  -3) decode & validate the token
     * @return DGZ_Response
     */
    public function validateToken()
    {
        $config = container(Config::class);
        $jwt_secret = $config->getConfig()['jwt-secret-key'];
        $encoding_algorithm = $config->getConfig()['encoding_algorithm'];
        $response = new DGZ_Response();

        // Get the Authorization header
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? null;

        if (!$authHeader) {
            $response->setStatus(401);
            $response->setHeader("WWW-Authenticate", "Bearer error='missing_token'");
            $response->setData([
                "code" => 401,
                "status" => false,
                "valid_token" => false,
                "expired_token" => false,
                "message" => "Missing Authorization header"
            ]);
            $this->validatedToken = false;
            $response->send();
            return $response;
        }


        // There is an Authorization header, so validate it
        // extract the token ie remove 'Bearer ' from the value of $authHeader 
        // (which comes like this: Bearer <token>)
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $jwt = $matches[1];
        } else {
            $response->setStatus(401);
            $response->setData([
                "code" => 401,
                "status" => false,
                "valid_token" => false,
                "expired_token" => false,
                "message" => "Invalid Authorization header format"
            ]);
            $this->validatedToken = false;
            $response->send();
            return $response;
        }

        // Decode and validate the token
        try {
            $decoded = JWT::decode($jwt, new Key($jwt_secret, $encoding_algorithm));
            $this->validatedUser = (array) $decoded->data; // contains user_id, etc.

            // ✅ token is valid
            $response->setStatus(200);
            $response->setData([
                "code" => 200,
                "status" => true,
                "valid_token" => true, 
                "expired_token" => false, 
                "message" => "Token is valid"
            ]);
            $this->validatedToken = true;
            return $response;
        }
        //catch whatever exceptions you wanna handle individually
        catch (InvalidArgumentException $e)
        {
            //the provided key/key-array is empty or malformed
            $response->setStatus(401);
            $response->setHeader("WWW-Authenticate", "Bearer error='failed_signature_validation'");
            $response->setData([
                "code" => 401,
                "status" => false,
                "valid_token" => false,
                "expired_token" => false,
                "message" => $e->getMessage()
            ]);
            $this->validatedToken = false;
            $response->send();
            return $response;
        }
        catch (ExpiredException $e) {
            $response->setStatus(401);
            $response->setHeader("WWW-Authenticate", "Bearer error='expired_token'");
            $response->setData([
                "code" => 401,
                "status" => false,
                "valid_token" => true,
                "expired_token" => true,
                "message" => "Access token is expired"
            ]);
            $this->validatedToken = false;
            $response->send();
            return $response;
        }
        catch (SignatureInvalidException $e) {
            $response->setStatus(401);
            $response->setHeader("WWW-Authenticate", "Bearer error='failed_signature_validation'");
            $response->setData([
                "code" => 401,
                "status" => false,
                "valid_token" => false,
                "expired_token" => false,
                "message" => "Invalid token signature"
            ]);
            $this->validatedToken = false;
            $response->send();
            return $response;
        }
        catch (UnexpectedValueException | DomainException | BeforeValidException $e) {
            $response->setStatus(401);
            $response->setHeader("WWW-Authenticate", "Bearer error='failed_signature_validation'");
            $response->setData([
                "code" => 401,
                "status" => false,
                "valid_token" => false,
                "expired_token" => false,
                "message" => "Invalid or malformed token"
            ]);
            $this->validatedToken = false;
            $response->send();
            return $response;
        }
    }




    /**
     * Check if token is valid, & not expired 
     * @param mixed $user_id
     * @param mixed $username
     * @return DGZ_Response
     */
    public function validateTokenDELETE($user_id, $username)
    {
        $config = container(Config::class);
        $jwt_secret = $config->getConfig()['jwt-secret-key'];
        $encoding_algorithm = $config->getConfig()['encoding_algorithm'];
        $appHomePage = $config->getHomePage();
        $response = new DGZ_Response();

        //prepare claims for the JWT
        $issuedAt = time();
        $accessTokenExp = $issuedAt + 900; // 1 hour
        $payload = [
            "iss" => $appHomePage,
            "aud" => $appHomePage,
            "iat" => $issuedAt,
            "exp" => $accessTokenExp,
            "data" => [
                    "user_id" => $user_id,
                    "username" => $username
            ]
        ];

        $jwt = JWT::encode($payload, $jwt_secret, $encoding_algorithm);
        //this $jwt is the very long string
        //Verify the request JWT token
        //First, is there an AUTHORIZATION header?
        $authHeader = $this->getServerVariable('Authorization');
        
        if (!$authHeader)
        {
            //return failed response
            $response->setStatus(401);
            $response->setData("Bearer error='invalid_token'");
            $response->setHeader("WWW-Authenticate", "Bearer error='missing_token'");
            $this->validatedToken = false;
            return $response; 
        }
        //If there is an Authorization header, validate it
        //Isolate the token ie remove 'Bearer ' from the value of $authHeader 
        // (eg Bearer sfsffgdgddsghdsghdssdsdgdshahahaha)   
        $token = preg_replace('/^Bearer\s*/', '', $authHeader);

        //try to decode the JWT token
        try { 
            $decoded = JWT::decode($token, new Key($jwt_secret,$encoding_algorithm));

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
            $response->setStatus(401);
            $response->setData($e->getMessage());
            $response->setHeader("WWW-Authenticate", "Bearer error='failed_signature_validation'");
            $this->validatedToken = false;
            return $response;
        }
        catch (DomainException $e)
        {
            //the provided algorithm is unsupported or...
            //the provided key is invalid or...
            //unknown error thrown in openssl or libsodium or...
            //libsodium is required but not available...
            $response->setStatus(401);
            $response->setData($e->getMessage());
            $response->setHeader("WWW-Authenticate", "Bearer error='failed_signature_validation'");
            $this->validatedToken = false;
            return $response;
        }
        catch (SignatureInvalidException $e)
        {
            //the provided JWT signature validation failed
            $response->setStatus(401);
            $response->setData("Authorization on signature failed");
            $response->setHeader("WWW-Authenticate", "Bearer error='failed_signature_validation'");
            $this->validatedToken = false;
            return $response;
        }
        catch (BeforeValidException $e)
        {
            //the provided JWT is trying to be used before "nbf" claim or...
            //the provided JWT is trying to be used before "iat" claim
            $response->setStatus(401);
            $response->setData($e->getMessage());
            $response->setHeader("WWW-Authenticate", "Bearer error='failed_signature_validation'");
            $this->validatedToken = false;
            return $response;
        }
        catch (ExpiredException $e)
        {
            //the provided JWT is trying to be used after "exp" claim
            $response->setStatus(401);
            $response->setData("Auth token has expired");
            $response->setHeader("WWW-Authenticate", "Bearer error='expired_token'");
            $this->validatedToken = false;
            return $response;
        }
        catch (UnexpectedValueException $e)
        {
            //the provided JWT is malformed or...
            //the provided JWT is missing an algorithm/using an unsupported algorithm or...
            //the provided JWT algorithm does not match provided key or...
            //the provided key ID in key/key-array is empty or invalid
            $response->setStatus(401);
            $response->setData("Auth token is invalid");
            $response->setHeader("WWW-Authenticate", "Bearer error='invalid_token'");
            $this->validatedToken = false;
            return $response;
        }
    }


    public function refreshToken()
    {
        $config = container(Config::class);
        $jwt_secret = $config->getConfig()['jwt-secret-key'];
        $encoding_algorithm = $config->getConfig()['encoding_algorithm'];
        $response = new DGZ_Response();

        // Get refresh token from Authorization header
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? null;

        // There is an Authorization header, so validate it
        // extract the token ie remove 'Bearer ' from the value of $authHeader 
        // (which comes like this: Bearer <token>)
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $response->setStatus(401);
            $response->setHeader("WWW-Authenticate", "Bearer error='missing_token'");
            $response->setData([
                "code" => 401,
                "status" => false,
                "valid_token" => false, 
                "expired_token" => false,
                "message" => "Missing or invalid token",
                "tokens" => []
            ]);
            $this->validatedToken = false;
            return $response;
        }

        $refreshToken = $matches[1];

        // Check if refresh token exists in DB (not revoked-revoking would mean it's deleted)
        // if so, check if it's not expired
        // if not, check if it matches the refresh token sent by client 
        // if it's expired, tell them to login again
        try {
            $decoded = JWT::decode($refreshToken, new Key($jwt_secret, $encoding_algorithm));
            
            // get the user_id from the refresh token
            $userId = $decoded->data->user_id;

            // check if it exists in DB
            $oldRefreshToken = $this->getRefreshToken($userId);
            if ($oldRefreshToken)
            {
                // it exists, so check if it matches refresh token from client
                if ($oldRefreshToken['refresh_token'] !== $refreshToken)
                {
                    throw new Exception("Refresh token mismatch");
                }
            }

            // generate new tokens
            $tokenData = $this->generateTokens($userId);

            // update refresh token in DB
            $this->updateRefreshToken($userId, $tokenData['refresh_token'], $tokenData['refresh_token_expiry']);

            // prepare to send back response
            $response->setStatus(200);
            $response->setData([
                "code" => 200,
                "status" => true,
                "valid_token" => true, 
                "expired_token" => false,
                "message" => "Tokens refreshed successfully",
                "tokens" => $tokenData,
                "user" => $userId
            ]);
            return $response;
        }
        catch (ExpiredException $e) {
            $response->setStatus(401);
            $response->setData([
                "code" => 401,
                "status" => false,
                "valid_token" => false, 
                "expired_token" => true,
                "message" => "Refresh token expired, please login again",
                "tokens" => []
            ]);
            return $response;
        }
        catch (Exception $e) {
            $response->setStatus(401);
            $response->setData([
                "code" => 401,
                "status" => false,
                "valid_token" => false, 
                "expired_token" => false,
                "message" => $e->getMessage(),
                "tokens" => []
            ]);
            return $response;
        }
    }



    private function saveRefreshToken($userId, $refreshToken, $expiry)
    {
        $refreshTokensObject = container(Refresh_tokens::class);
        /////$refreshTokensObject = new Refresh_tokens();
        $refreshTokensObject->user_id = $userId;
        $refreshTokensObject->refresh_token = $refreshToken;
        $refreshTokensObject->refresh_token_expiry = $expiry;
        $refreshTokensObject->save();
        return true;
    }

    private function getRefreshToken($userId)
    {
        $refreshTokensObject = container(Refresh_tokens::class);
        /////$refreshTokensObject = new Refresh_tokens();
        $where = ['user_id' => $userId];
        return $refreshTokensObject->selectWhere(['refresh_token', 'refresh_token_expiry'], $where);
    }

     private function updateRefreshToken($userId, $newRefreshToken, $newExpiry)
    {
        $refreshTokensObject = container(Refresh_tokens::class);
        /////$refreshTokensObject = new Refresh_tokens();
        $refreshTokensObject->refresh_token = $newRefreshToken;
        $refreshTokensObject->refresh_token_expiry = $newExpiry;

         $where = ['user_id' => $userId];
        $refreshTokensObject->update($where);
        return true;
    }
}

?>