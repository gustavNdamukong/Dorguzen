<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('dgz_failed_jobs', function (Blueprint $table) {
            $table->id();

            $table->string('queue')->default('default');
            $table->longText('payload');
            $table->string('exception');
            $table->longText('exception_trace');
            $table->unsignedInteger('attempts');
            $table->timestamp('failed_at');
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('dgz_failed_jobs')
        );
    }
};
