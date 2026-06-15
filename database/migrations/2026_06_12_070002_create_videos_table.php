<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('videos', function (Blueprint $table) {
            $table->id('video_id');
            $table->integer('album_id')->unsigned()->notNullable();
            $table->string('video_title', 255)->notNullable();
            $table->text('video_description')->nullable();
            $table->enum('video_source', ['youtube', 'vimeo'])->notNullable();
            $table->string('video_ref', 255)->notNullable();
            $table->integer('video_sort_order')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('videos')
        );
    }
};
