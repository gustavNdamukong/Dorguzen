<?php

namespace Dorguzen\Services;

use Dorguzen\Models\Users;
use Dorguzen\Models\Logs;
use Dorguzen\Models\BaseSettings;
use Dorguzen\Models\ContactFormMessage;
use Dorguzen\Core\DGZ_Validate;

/**
 * AdminService
 *
 * Owns all database operations and business logic for the admin area.
 *
 * Controllers served:
 *   - AdminController (dashboard, manageUsers, createUser, editUser,
 *                      adminUserChangePw, deleteUser, contactMessages,
 *                      deleteContactMessage, baseSettings, log,
 *                      log_errors_only, logAdminLogins)
 *
 * The controller keeps: view rendering, input validation, session
 * updates, pagination markers, and redirects.
 */
class AdminService
{
    private Users              $users;
    private Logs               $logs;
    private BaseSettings       $baseSettings;
    private ContactFormMessage $contactFormMessage;

    public function __construct(
        Users              $users,
        Logs               $logs,
        BaseSettings       $baseSettings,
        ContactFormMessage $contactFormMessage
    ) {
        $this->users              = $users;
        $this->logs               = $logs;
        $this->baseSettings       = $baseSettings;
        $this->contactFormMessage = $contactFormMessage;
    }


    // ---------------------------------------------------------------
    // View payload builders
    // ---------------------------------------------------------------

    /**
     * Build the viewModel for AdminController::dashboard() / manageUsers().
     */
    public function buildManageUsersPayload(): array
    {
        $allUsers = $this->getAllUsers();
        return [
            'allUsers'      => $allUsers,
            'numOfAllUsers' => count($allUsers),
        ];
    }

    /**
     * Build the viewModel for AdminController::log().
     */
    public function buildLogPayload(): array
    {
        return [
            'logs' => $this->getRecentLogs(50),
        ];
    }

    /**
     * Build the viewModel for AdminController::logAdminLogins().
     */
    public function buildAdminLoginsPayload(): array
    {
        return [
            'adminLoginData' => $this->getAdminLoginLogs(),
        ];
    }

    /**
     * Build the viewModel for AdminController::log_errors_only().
     * Merges the raw log rows with the pagination markers the controller computed.
     *
     * @param array $runtimeErrorLogs  Rows returned by getRuntimeErrorLogs().
     * @param array $pagination        Array returned by getPaginationMarkers().
     */
    public function buildErrorsLogPayload(array $runtimeErrorLogs, array $pagination): array
    {
        return array_merge(
            ['runtime_error_logs_raw' => $runtimeErrorLogs],
            $pagination
        );
    }

    /**
     * Build the viewModel for AdminController::contactMessages().
     */
    public function buildContactMessagesPayload(): array
    {
        return [
            'contactMessages' => $this->getAllContactMessages(),
        ];
    }


    // ---------------------------------------------------------------
    // Validation
    // ---------------------------------------------------------------

    /**
     * Validate the create-user form.
     *
     * Checks required fields, password match, and email uniqueness.
     * The uniqueness check is skipped when field-level errors are already present
     * to avoid an unnecessary DB round-trip.
     * Returns an HTML error string on failure, empty string when valid.
     */
    public function validateCreateUserInput(
        string $fn,
        string $ln,
        string $email,
        string $password,
        string $confirm
    ): string {
        $val  = new DGZ_Validate();
        $fail  = $val->validate_firstname($fn);
        $fail .= $val->validate_surname($ln);
        $fail .= $val->validate_email($email);
        $fail .= $val->validate_password($password);

        if ($password !== $confirm) {
            $fail .= "Both passwords did not match!\n";
        }

        if ($fail === '' && $this->isEmailTaken($email)) {
            $fail .= 'That email address is already registered on this system';
        }

        return $fail;
    }

    /**
     * Validate the edit-user form.
     * Returns an HTML error string on failure, empty string when valid.
     */
    public function validateEditUserInput(
        string $fn,
        string $ln,
        string $email,
        string $password
    ): string {
        $val  = new DGZ_Validate();
        $fail  = $val->validate_firstname($fn);
        $fail .= $val->validate_surname($ln);
        $fail .= $val->validate_email($email);
        $fail .= $val->validate_password($password);
        return $fail;
    }

    /**
     * Validate the admin change-password form.
     * Returns an HTML error string on failure, empty string when valid.
     */
    public function validateChangePasswordInput(string $email, string $password): string
    {
        $val  = new DGZ_Validate();
        $fail  = $val->validate_email($email);
        $fail .= $val->validate_password($password);
        return $fail;
    }


    // ---------------------------------------------------------------
    // Users
    // ---------------------------------------------------------------

    /**
     * Return all user rows, used by the dashboard and manageUsers views.
     */
    public function getAllUsers(): array
    {
        return $this->users->getAll() ?: [];
    }

    /**
     * Return a single user row by ID, or null if not found.
     *
     * @return array|null  First row from getUserById(), or null.
     */
    public function getUserById(int $userId): ?array
    {
        $result = $this->users->getUserById($userId);
        return $result[0] ?? null;
    }

    /**
     * Check whether an email address is already registered.
     */
    public function isEmailTaken(string $email): bool
    {
        $result = $this->users->query(
            "SELECT users_id FROM users WHERE users_email = '$email'"
        );
        return !empty($result);
    }

    /**
     * Create a new user via the Users model.
     *
     * @return int|bool  New row ID on success | 1062 on duplicate email | false on failure.
     */
    public function createUser(
        string $userType,
        string $firstname,
        string $lastname,
        string $email,
        string $phone,
        string $password
    ): int|bool {
        $created = $this->users->timeNow();
        return $this->users->createUser(
            $userType, $firstname, $lastname, $email, $phone, $password, $created
        );
    }

    /**
     * Update an existing user's profile fields.
     *
     * @param int   $userId  The user to update.
     * @param array $data    Map of column => value pairs to update.
     * @return bool
     */
    public function updateUser(int $userId, array $data): bool
    {
        return (bool) $this->users->updateObject($data, ['users_id' => $userId]);
    }

    /**
     * Update a user's email and password (admin change-password flow).
     * Returns the refreshed user row so the controller can update the session.
     *
     * @return array|null  The updated user row, or null if the update failed.
     */
    public function updateUserPassword(int $userId, string $email, string $password): ?array
    {
        $user = container(Users::class);
        $user->users_email = $email;
        $user->users_pass  = $password;
        $updated = $user->update(['users_id' => $userId]);

        if (!$updated) {
            return null;
        }

        $result = $this->users->getUserById($userId);
        return $result[0] ?? null;
    }

    /**
     * Delete a user by ID.
     */
    public function deleteUser(int $userId): bool
    {
        return (bool) $this->users->deleteWhere(['users_id' => $userId]);
    }


    // ---------------------------------------------------------------
    // Contact form messages
    // ---------------------------------------------------------------

    /**
     * Return all contact messages, newest first.
     */
    public function getAllContactMessages(): array
    {
        return $this->contactFormMessage->getAll('contactformmessage_date DESC') ?: [];
    }

    /**
     * Delete a single contact message by ID.
     */
    public function deleteContactMessage(int $id): bool
    {
        return (bool) $this->contactFormMessage->deleteWhere(['contactformmessage_id' => $id]);
    }


    // ---------------------------------------------------------------
    // Base settings
    // ---------------------------------------------------------------

    /**
     * Return all base settings rows.
     */
    public function getAllBaseSettings(): array
    {
        return $this->baseSettings->getAll() ?: [];
    }

    /**
     * Persist a batch of settings from a POST array.
     * Each key is a settings_name; each value is its new settings_value.
     *
     * @param array $fieldsValues  e.g. ['site_name' => 'My App', 'theme' => 'dark']
     */
    public function updateBaseSettings(array $fieldsValues): void
    {
        $table = $this->baseSettings->getTable();
        foreach ($fieldsValues as $field => $value) {
            $this->baseSettings->query(
                "UPDATE {$table} SET settings_value = '$value' WHERE settings_name = '$field'"
            );
        }
    }


    // ---------------------------------------------------------------
    // Logs
    // ---------------------------------------------------------------

    /**
     * Return the most recent log entries.
     *
     * @param int $limit  Maximum number of rows to return (default 50).
     */
    public function getRecentLogs(int $limit = 50): array
    {
        return $this->logs->getAll('logs_created DESC', $limit) ?: [];
    }

    /**
     * Return all runtime-error log entries, newest first.
     * The controller passes these through getPaginationMarkers() after receiving them.
     */
    public function getRuntimeErrorLogs(): array
    {
        return $this->logs->getRunTimeErrors('logs_created DESC') ?: [];
    }

    /**
     * Return all admin-login log entries, newest first.
     */
    public function getAdminLoginLogs(): array
    {
        return $this->logs->getAdminLoginData('logs_created DESC') ?: [];
    }
}
