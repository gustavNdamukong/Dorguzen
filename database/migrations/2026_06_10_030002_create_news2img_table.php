<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('news2img', function (Blueprint $table) {
            $table->unsignedInteger('news_id');
            $table->unsignedInteger('images_id');
            $table->primaryKey('news_id');
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('news2img')
        );
    }
};
