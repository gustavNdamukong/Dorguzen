<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('contactformmessage', function (Blueprint $table) {
            $table->id('contactformmessage_id');

            $table->string('contactformmessage_name', 50);
            $table->string('contactformmessage_email', 50)->nullable();
            $table->string('contactformmessage_phone', 50)->nullable();
            $table->string('contactformmessage_message', 1000);
            $table->timestamp('contactformmessage_date')->useCurrent();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('contactformmessage')
        );
    }
};
