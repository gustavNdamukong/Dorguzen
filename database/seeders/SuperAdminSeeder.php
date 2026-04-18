<?php

namespace Dorguzen\Database\Seeders;

use Dorguzen\Core\Database\Seeders\Seeder;

/**
 * Seeds the default Dorguzen super-admin account.
 *
 * Safe to run multiple times — INSERT IGNORE means the row is skipped
 * if the email already exists (unique constraint on users_email).
 *
 * After first setup, log in with:
 *   Email:    admin@dorguzen.com
 *   Password: Admin1234!
 *
 * ⚠️  Change these credentials immediately after your first login.
 */
class SuperAdminSeeder extends Seeder
{
    protected string $table = 'users';

    public function run(): void
    {
        $key = env('DB_KEY', '');

        $sql = "INSERT IGNORE INTO `users` (
                    `users_type`,
                    `users_email`,
                    `users_pass`,
                    `users_first_name`,
                    `users_last_name`,
                    `users_phone_number`,
                    `users_emailverified`,
                    `users_created`
                ) VALUES (
                    'super_admin',
                    'admin@dorguzen.com',
                    AES_ENCRYPT(?, ?),
                    'Dorguzen',
                    'Admin',
                    '',
                    'yes',
                    NOW()
                )";

        $this->db->execute($sql, ['Admin123', $key]);
    }

    public function getTable(): string
    {
        return $this->table;
    }
}