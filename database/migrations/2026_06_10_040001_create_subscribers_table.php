<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('subscribers', function (Blueprint $table) {
            $table->id('subscriber_id');
            $table->string('subscriber_email', 255);
            $table->unique('subscriber_email');
            $table->string('subscriber_firstname', 100)->nullable();
            $table->tinyInteger('subscriber_welcomed')->default(0);
            $table->tinyInteger('subscriber_active')->default(1);
            $table->timestamp('subscriber_created')->useCurrent();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('subscribers')
        );
    }
};
