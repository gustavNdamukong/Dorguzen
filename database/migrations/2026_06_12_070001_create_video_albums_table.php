<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('video_albums', function (Blueprint $table) {
            $table->id('album_id');
            $table->string('album_name', 255)->notNullable();
            $table->string('album_slug', 255)->notNullable()->unique();
            $table->text('album_description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('video_albums')
        );
    }
};
