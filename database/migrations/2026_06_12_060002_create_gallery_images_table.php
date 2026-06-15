<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('gallery_images', function (Blueprint $table) {
            $table->id('image_id');
            $table->integer('album_id')->unsigned();
            $table->string('image_filename', 255);
            $table->string('image_caption', 500)->nullable();
            $table->integer('image_sort_order')->unsigned()->default(0);
            $table->timestamp('created_at')->useCurrent();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('gallery_images')
        );
    }
};
