<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

/**
 * Core framework migration — ships with Dorguzen.
 *
 * Creates the refresh_tokens table used by DGZ_APITrait to persist
 * JWT refresh tokens server-side. One row per user (enforced by the
 * unique index on user_id); the trait upserts the row on every login.
 */
return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('refresh_tokens', function (Blueprint $table) {
            $table->id('id');

            // The user this token belongs to
            $table->unsignedInteger('user_id');

            // The signed JWT refresh token string
            $table->string('token', 512);

            // When this token expires
            $table->dateTime('expires_at');

            $table->timestamp('created_at');

            // One refresh token per user — old token is replaced on each login
            $table->index('user_id');
            $table->index('token');
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('refresh_tokens')
        );
    }
};
