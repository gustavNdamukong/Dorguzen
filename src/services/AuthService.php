<?php

namespace Dorguzen\Services;

use Dorguzen\Models\Users;
use Dorguzen\Models\Logs;
use Dorguzen\Models\Password_reset;
use Dorguzen\Core\DGZ_Validate;
use Dorguzen\Core\DGZ_CheckPassword;

/**
 * AuthService
 *
 * Owns all database operations and business logic for the authentication
 * flow: registration, email verification, login, password reset.
 *
 * Controllers served:
 *   - AuthController  (register, verifyEmail, doLogin, reset, resetPw)
 *
 * The controller keeps: view rendering, input validation, session
 * management, email sending, and redirects. Nothing here touches
 * $_POST, $_SESSION, or headers.
 */
class AuthService
{
    private Users          $users;
    private Logs           $logs;
    private Password_reset $passwordReset;

    public function __construct(Users $users, Logs $logs, Password_reset $passwordReset)
    {
        $this->users         = $users;
        $this->logs          = $logs;
        $this->passwordReset = $passwordReset;
    }


    // ---------------------------------------------------------------
    // Logging
    // ---------------------------------------------------------------

    /**
     * Write a bot/spam attempt log entry.
     */
    public function logBotAttempt(string $title, string $message): void
    {
        $this->logs->log($title, $message);
    }


    // ---------------------------------------------------------------
    // Registration
    // ---------------------------------------------------------------

    /**
     * Validate registration input.
     *
     * Checks all required fields, password strength, and password confirmation.
     * Returns an HTML error string on failure, or an empty string when valid.
     * Sanitization of raw HTTP input is the caller's responsibility.
     */
    public function validateRegistrationInput(
        string $firstname,
        string $surname,
        string $password,
        string $confirm,
        string $email
    ): string {
        $val  = new DGZ_Validate();
        $fail  = $val->validate_firstname($firstname);
        $fail .= $val->validate_surname($surname);
        $fail .= $val->validate_password($password);
        $fail .= $val->validate_email($email);

        if ($fail === '') {
            $checkPwd = new DGZ_CheckPassword($password, 6);
            if (!$checkPwd->check()) {
                foreach ($checkPwd->getErrors() as $error) {
                    $fail .= $error;
                }
            }
            if ($password !== $confirm) {
                $fail .= "Your passwords don't match.";
            }
        }

        return $fail;
    }

    /**
     * Persist a new user row.
     *
     * @param array $data {
     *   user_type, email, password, firstname, surname,
     *   phone, emailverified, activationCode
     * }
     * @return int|bool  New row ID on success | 1062 on duplicate email | false on failure.
     */
    public function registerNewUser(array $data): int|bool
    {
        $user = container(Users::class);

        $user->users_type             = $data['user_type'];
        $user->users_email            = $data['email'];
        $user->users_pass             = $data['password'];
        $user->users_first_name       = $data['firstname'];
        $user->users_last_name        = $data['surname'];
        $user->users_phone_number     = $data['phone'];
        $user->users_emailverified    = $data['emailverified'];
        $user->users_eactivationcode  = $data['activationCode'];
        $user->users_created          = $user->timeNow();

        return $user->save();
    }


    // ---------------------------------------------------------------
    // Email verification
    // ---------------------------------------------------------------

    /**
     * Look up a user by their email-activation code.
     *
     * @return array{users_id: int, users_first_name: string, users_email: string}|null
     */
    public function getUserByActivationCode(string $code): ?array
    {
        $rows = $this->users->selectWhere(
            ['users_id', 'users_first_name', 'users_email'],
            ['users_eactivationcode' => $code]
        );

        return $rows ?: null;
    }

    /**
     * Mark a user's email as verified and clear their activation code.
     *
     * @return bool  True if the DB row was updated.
     */
    public function activateUserEmail(string $code): bool
    {
        $user = container(Users::class);
        $user->users_emailverified   = 'yes';
        $user->users_eactivationcode = null;

        return (bool) $user->update(['users_eactivationcode' => $code]);
    }


    // ---------------------------------------------------------------
    // Login
    // ---------------------------------------------------------------

    /**
     * Validate login credentials.
     *
     * Checks that the email is well-formed and the password field is non-empty.
     * Returns an HTML error string on failure, or an empty string when valid.
     * Sanitization of raw HTTP input is the caller's responsibility before
     * passing values here.
     */
    public function validateLoginInput(string $email, string $password): string
    {
        $val  = new DGZ_Validate();
        $fail  = $val->validate_email($email);
        $fail .= $val->validate_password($password);
        return $fail;
    }

    /**
     * Validate the forgot-password email submission.
     * Returns an HTML error string on failure, or an empty string when valid.
     */
    public function validateForgotPasswordInput(string $email): string
    {
        $val = new DGZ_Validate();
        return $val->validate_email($email);
    }

    /**
     * Validate the password-reset form submission.
     * Returns an HTML error string on failure, or an empty string when valid.
     */
    public function validatePasswordResetInput(
        string $userId,
        string $email,
        string $password,
        string $confirm
    ): string {
        $val  = new DGZ_Validate();
        $fail = '';
        if (!$userId) $fail .= '<p>Something went wrong! Try requesting a reset again.</p>';
        if (!$email)  $fail .= '<p>Sorry! We could not identify your account.</p>';
        $fail .= $val->validate_password($password);
        if ($password !== $confirm) {
            $fail .= '<p>Both passwords did not match!</p>';
        }
        return $fail;
    }

    /**
     * Authenticate a user by email and password.
     *
     * @return array|false  Full user row on success, false on failure.
     */
    public function authenticateUser(string $email, string $password): array|false
    {
        return $this->users->authenticateUser([
            'users_email' => $email,
            'users_pass'  => $password,
        ]);
    }

    /**
     * Log an admin-level login event.
     * Call only after a successful authentication for admin/admin_gen/super_admin users.
     */
    public function logAdminLogin(array $userData): void
    {
        $logData = 'User type: '  . $userData['users_type']       .
                   ' | Name: '    . $userData['users_first_name'] .
                   ' '            . $userData['users_last_name']  .
                   ' | Time: '    . date('d-m-y h:i:s');

        $this->logs->log('Admin login', $logData);
    }


    // ---------------------------------------------------------------
    // Password reset
    // ---------------------------------------------------------------

    /**
     * Look up a verified user by email for the "forgot password" flow.
     *
     * @return array{users_id: int, users_email: string, users_firstname: string}|null
     */
    public function getUserForPasswordReset(string $email): ?array
    {
        $found = $this->users->recoverLostPw($email);
        return $found ?: null;
    }

    /**
     * Insert a password-reset record so the user can reset via emailed link.
     * The reset code is generated in the controller and passed in here.
     *
     * @return bool  True if the row was inserted.
     */
    public function savePasswordResetRecord(array $found, string $resetCode): bool
    {
        $record = container(Password_reset::class);

        $record->password_reset_users_id  = $found['users_id'];
        $record->password_reset_firstname = $found['users_firstname'];
        $record->password_reset_email     = $found['users_email'];
        $record->password_reset_date      = date('Y-m-d H:i:s');
        $record->password_reset_reset_code = $resetCode;

        return (bool) $record->save();
    }

    /**
     * Fetch and immediately consume a password-reset token.
     * The record is deleted on retrieval so it cannot be reused.
     *
     * @return array|null  The password_reset row, or null if not found.
     */
    public function fetchAndConsumePasswordReset(string $resetCode): ?array
    {
        $sql     = "SELECT * FROM password_reset
                    WHERE password_reset_reset_code = '$resetCode'";
        $results = $this->passwordReset->query($sql);

        if (empty($results)) {
            return null;
        }

        $record = $results[0];

        $this->passwordReset->query(
            "DELETE FROM password_reset
             WHERE password_reset_id = " . (int) $record['password_reset_id'] .
            " AND password_reset_reset_code = '$resetCode'"
        );

        return $record;
    }

    /**
     * Update a user's password.
     *
     * @param string $userId  The user's ID (from the reset form).
     * @param string $email   The user's email (for identity confirmation).
     * @param string $pwd     The new plain-text password.
     * @return bool
     */
    public function resetUserPassword(string $userId, string $email, string $pwd): bool
    {
        return (bool) $this->users->resetUserPassword($userId, $email, $pwd);
    }
}
