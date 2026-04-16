<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('logs', function (Blueprint $table) {
            $table->id('logs_id');

            $table->string('logs_title', 100);
            $table->text('logs_message');

            // Optional structured context data (JSON blob)
            $table->text('context_json')->nullable();
            
            $table->timestamp('logs_created')->useCurrent();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('logs')
        );
    }
};
