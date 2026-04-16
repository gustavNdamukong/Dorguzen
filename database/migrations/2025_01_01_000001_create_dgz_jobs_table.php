<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('dgz_jobs', function (Blueprint $table) {
            $table->id();

            $table->string('queue')->default('default');
            $table->longText('payload');

            $table->unsignedInteger('attempts')->default(0);
            $table->unsignedInteger('max_attempts')->default(3);

            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('available_at');
            $table->timestamp('failed_at')->nullable();

            $table->timestamp('created_at');
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('dgz_jobs')
        );
    }
};
