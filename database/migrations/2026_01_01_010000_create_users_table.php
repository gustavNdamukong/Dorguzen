<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('users', function (Blueprint $table) {
            $table->id('users_id');

            $table->enum('users_type', ['member', 'admin', 'admin_gen', 'super_admin'])
                  ->default('member');

            $table->string('users_email', 80)->unique();
            $table->string('users_phone_number', 15)->nullable();

            // Stored as a blob to hold hashed/encrypted values of arbitrary length
            $table->binary('users_pass');

            $table->string('users_first_name', 20);
            $table->string('users_last_name', 40);

            $table->enum('users_emailverified', ['yes', 'no'])->default('no');
            $table->string('users_eactivationcode', 100)->nullable();

            $table->timestamp('users_updated')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('users_created')->default('0000-00-00 00:00:00');
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('users')
        );
    }
};
