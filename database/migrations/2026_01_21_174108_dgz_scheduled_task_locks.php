<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('dgz_scheduled_task_locks', function (Blueprint $table) {
            $table->primaryKey('task_key');
            $table->timestamp('locked_at')->notNullable();
            $table->timestamp('expires_at')->notNullable();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $sql = $this->schema->dropIfExists('dgz_scheduled_task_locks');
        $this->addStatement($sql);
    }
};
