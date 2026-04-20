<?php

namespace Dorguzen\Api\V1\Controllers;

use Dorguzen\Core\DGZ_Controller;
use Dorguzen\Core\DGZ_APITrait;
use Dorguzen\Core\DGZ_Response;
use Dorguzen\Core\DGZ_Validate;
use Dorguzen\Core\DGZ_Messenger;
use Dorguzen\Services\AuthService;
use Dorguzen\Config\Config;
use OpenApi\Attributes as OA;

/**
 * Handles authentication API routes.
 *
 * Routes (register in routes/api.php):
 *   POST /api/v1/auth/register
 *   POST /api/v1/auth/login
 *   POST /api/v1/auth/refresh
 */
class AuthApiController extends DGZ_Controller
{
    use DGZ_APITrait;

    private AuthService $authService;

    public function __construct()
    {
        parent::__construct();
        $this->authService = container(AuthService::class);
    }

    public function getDefaultAction()
    {
        return 'defaultAction';
    }

    public function defaultAction()
    {}


    // -------------------------------------------------------------------------
    // POST /api/v1/auth/register
    // -------------------------------------------------------------------------

    #[OA\Post(
        path: '/api/v1/auth/register',
        operationId: 'authRegister',
        summary: 'Register a new user',
        description: 'Creates a new user account and immediately returns JWT access and refresh tokens. '
            . 'An activation email is sent — the account cannot be used for login until the email link is clicked.',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['firstname', 'surname', 'password', 'confirm_password', 'phone', 'email'],
                properties: [
                    new OA\Property(property: 'firstname',        type: 'string',  example: 'John'),
                    new OA\Property(property: 'surname',          type: 'string',  example: 'Doe'),
                    new OA\Property(property: 'password',         type: 'string',  format: 'password', example: 'Secret123!'),
                    new OA\Property(property: 'confirm_password', type: 'string',  format: 'password', example: 'Secret123!'),
                    new OA\Property(property: 'phone',            type: 'string',  example: '650000000'),
                    new OA\Property(property: 'email',            type: 'string',  format: 'email',    example: 'john@example.com'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'User registered successfully',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'code',           type: 'integer', example: 201),
                    new OA\Property(property: 'status',         type: 'boolean', example: true),
                    new OA\Property(property: 'message',        type: 'string',  example: 'Registration successful. Please check your email to activate your account.'),
                    new OA\Property(property: 'activationLink', type: 'string',  example: 'http://localhost/dorguzen/auth/verifyEmail?em=abc123'),
                    new OA\Property(property: 'tokens',         type: 'object',
                        properties: [
                            new OA\Property(property: 'access_token',          type: 'string'),
                            new OA\Property(property: 'access_token_expiry',   type: 'integer'),
                            new OA\Property(property: 'refresh_token',         type: 'string'),
                            new OA\Property(property: 'refresh_token_expiry',  type: 'integer'),
                        ]
                    ),
                ])
            ),
            new OA\Response(response: 409, description: 'Conflict — email already registered'),
            new OA\Response(response: 422, description: 'Unprocessable — validation failed'),
            new OA\Response(response: 500, description: 'Server error'),
        ]
    )]
    public function register(): void
    {
        $this->setHeaders();
        $body = $this->getRequestBody();
        $val  = new DGZ_Validate();

        $firstname  = $val->fix_string($body['firstname']        ?? '');
        $surname    = $val->fix_string($body['surname']          ?? '');
        $password   = $val->fix_string($body['password']         ?? '');
        $confirmPwd = $val->fix_string($body['confirm_password'] ?? '');
        $phone      = $val->fix_string($body['phone']            ?? '');
        $email      = $val->fix_string($body['email']            ?? '');

        $fail = $this->authService->validateRegistrationInput($firstname, $surname, $password, $confirmPwd, $email);

        if ($fail !== '') {
            $this->sendError(422, $fail);
            return;
        }

        $activationCode = md5(uniqid((string) rand(), true));

        $saved = $this->authService->registerNewUser([
            'user_type'      => 'member',
            'email'          => $email,
            'password'       => $password,
            'firstname'      => $firstname,
            'surname'        => $surname,
            'phone'          => $phone,
            'emailverified'  => 'no',
            'activationCode' => $activationCode,
        ]);

        if ($saved === 1062) {
            $this->sendError(409, 'That email address is already registered.');
            return;
        }

        if (!$saved) {
            $this->sendError(500, 'Registration failed. Please try again.');
            return;
        }

        $userId = (int) $saved;

        $config         = container(Config::class);
        $appUrl         = $config->getHomePage();
        $appName        = $config->getConfig()['appName'];
        $activationLink = "{$appUrl}auth/verifyEmail?em={$activationCode}";

        $messenger = new DGZ_Messenger();
        $subject   = "Activate your {$appName} account";
        $message   = "<h2>Welcome to {$appName}!</h2>
                      <p>Please activate your account by clicking the link below:</p>
                      <p><a href='{$activationLink}'>{$activationLink}</a></p>
                      <p>If you did not create this account, you can safely ignore this email.</p>";
        $messenger->sendEmailActivationEmail('', $email, $subject, $message);

        $tokens = $this->generateTokens($userId);
        $this->saveRefreshToken($userId, $tokens['refresh_token'], $tokens['refresh_token_expiry']);

        (new DGZ_Response())
            ->setStatus(201)
            ->setData([
                'code'           => 201,
                'status'         => true,
                'message'        => 'Registration successful. Please check your email to activate your account.',
                'activationLink' => $activationLink,
                'tokens'         => $tokens,
            ])
            ->send();
        exit();
    }


    // -------------------------------------------------------------------------
    // POST /api/v1/auth/login
    // -------------------------------------------------------------------------

    #[OA\Post(
        path: '/api/v1/auth/login',
        operationId: 'authLogin',
        summary: 'Log in an existing user',
        description: 'Authenticates the user by email and password, returning JWT access and refresh tokens.',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email',    type: 'string', format: 'email',    example: 'admin@dorguzen.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'Admin123'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'code',    type: 'integer', example: 200),
                    new OA\Property(property: 'status',  type: 'boolean', example: true),
                    new OA\Property(property: 'message', type: 'string',  example: 'Login successful.'),
                    new OA\Property(property: 'user',    type: 'object',
                        properties: [
                            new OA\Property(property: 'id',        type: 'integer', example: 1),
                            new OA\Property(property: 'email',     type: 'string',  example: 'john@example.com'),
                            new OA\Property(property: 'firstname', type: 'string',  example: 'John'),
                            new OA\Property(property: 'lastname',  type: 'string',  example: 'Doe'),
                        ]
                    ),
                    new OA\Property(property: 'tokens', type: 'object',
                        properties: [
                            new OA\Property(property: 'access_token',         type: 'string'),
                            new OA\Property(property: 'access_token_expiry',  type: 'integer'),
                            new OA\Property(property: 'refresh_token',        type: 'string'),
                            new OA\Property(property: 'refresh_token_expiry', type: 'integer'),
                        ]
                    ),
                ])
            ),
            new OA\Response(response: 401, description: 'Unauthorised — invalid credentials'),
            new OA\Response(response: 422, description: 'Unprocessable — missing email or password'),
        ]
    )]
    public function login(): void
    {
        $this->setHeaders();
        $body = $this->getRequestBody();
        $val  = new DGZ_Validate();

        $email    = $val->fix_string($body['email']    ?? '');
        $password = $val->fix_string($body['password'] ?? '');

        $fail = $this->authService->validateLoginInput($email, $password);

        if ($fail !== '') {
            $this->sendError(422, $fail);
            return;
        }

        $user = $this->authService->authenticateUser($email, $password);

        if (!$user) {
            $this->sendError(401, 'Invalid email or password.');
            return;
        }

        $userId = (int) $user['users_id'];
        $tokens = $this->generateTokens($userId);

        $existing = $this->getRefreshToken($userId);
        if ($existing) {
            $this->updateRefreshToken($userId, $tokens['refresh_token'], $tokens['refresh_token_expiry']);
        } else {
            $this->saveRefreshToken($userId, $tokens['refresh_token'], $tokens['refresh_token_expiry']);
        }

        (new DGZ_Response())
            ->setStatus(200)
            ->setData([
                'code'    => 200,
                'status'  => true,
                'message' => 'Login successful.',
                'user'    => [
                    'id'        => $userId,
                    'email'     => $user['users_email'],
                    'firstname' => $user['users_first_name'],
                    'lastname'  => $user['users_last_name'],
                ],
                'tokens'  => $tokens,
            ])
            ->send();
        exit();
    }


    // -------------------------------------------------------------------------
    // POST /api/v1/auth/refresh
    // -------------------------------------------------------------------------

    #[OA\Post(
        path: '/api/v1/auth/refresh',
        operationId: 'authRefresh',
        summary: 'Refresh an expired access token',
        description: 'Accepts a valid refresh token in the Authorization header and returns a new access token and refresh token pair.',
        tags: ['Authentication'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token refreshed successfully',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'code',    type: 'integer', example: 200),
                    new OA\Property(property: 'status',  type: 'boolean', example: true),
                    new OA\Property(property: 'message', type: 'string',  example: 'Token refreshed successfully.'),
                    new OA\Property(property: 'tokens',  type: 'object',
                        properties: [
                            new OA\Property(property: 'access_token',         type: 'string'),
                            new OA\Property(property: 'access_token_expiry',  type: 'integer'),
                            new OA\Property(property: 'refresh_token',        type: 'string'),
                            new OA\Property(property: 'refresh_token_expiry', type: 'integer'),
                        ]
                    ),
                ])
            ),
            new OA\Response(response: 401, description: 'Unauthorised — refresh token missing, invalid, or expired'),
        ]
    )]
    public function refresh(): void
    {
        $this->setHeaders();

        $userId = $this->validateToken();

        if (!$userId) {
            $this->sendError(401, 'Invalid or expired refresh token.');
            return;
        }

        $tokens = $this->generateTokens($userId);
        $this->updateRefreshToken($userId, $tokens['refresh_token'], $tokens['refresh_token_expiry']);

        (new DGZ_Response())
            ->setStatus(200)
            ->setData([
                'code'    => 200,
                'status'  => true,
                'message' => 'Token refreshed successfully.',
                'tokens'  => $tokens,
            ])
            ->send();
        exit();
    }


    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function getRequestBody(): array
    {
        $json = request()->json();
        return is_array($json) ? $json : $_POST;
    }

    private function sendError(int $statusCode, string $message): void
    {
        (new DGZ_Response())
            ->setStatus($statusCode)
            ->setData([
                'code'    => $statusCode,
                'status'  => false,
                'message' => $message,
            ])
            ->send();
        exit();
    }
}
