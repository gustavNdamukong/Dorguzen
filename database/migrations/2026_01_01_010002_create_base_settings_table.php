<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('baseSettings', function (Blueprint $table) {
            $table->id('settings_id');

            $table->string('settings_name', 300);
            $table->string('settings_value', 300);
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('baseSettings')
        );
    }
};
