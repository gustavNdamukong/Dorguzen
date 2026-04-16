<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('password_reset', function (Blueprint $table) {
            $table->id('password_reset_id');

            $table->integer('password_reset_users_id', 10);
            $table->string('password_reset_firstname', 50);
            $table->string('password_reset_email', 50);

            // High-precision timestamp to avoid collisions on rapid resets
            $table->timestamp('password_reset_date', 6)->useCurrent()->useCurrentOnUpdate();

            $table->string('password_reset_reset_code', 100);
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('password_reset')
        );
    }
};
