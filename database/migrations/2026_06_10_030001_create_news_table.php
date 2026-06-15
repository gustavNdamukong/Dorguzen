<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('news', function (Blueprint $table) {
            $table->id('news_id');
            $table->string('news_title', 255);
            $table->text('news_description');
            $table->string('news_image', 500)->nullable();
            $table->string('news_video_url', 500)->nullable();
            $table->string('news_audio_url', 500)->nullable();
            $table->enum('news_status', ['published', 'draft'])->default('draft');
            $table->timestamp('news_created')->useCurrent();
            $table->timestamp('news_updated')->useCurrent()->useCurrentOnUpdate();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('news')
        );
    }
};
