<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('gallery_albums', function (Blueprint $table) {
            $table->id('album_id');
            $table->string('album_name', 150);
            $table->string('album_slug', 150)->unique();
            $table->text('album_description')->nullable();
            $table->string('album_cover', 255)->nullable();
            $table->string('album_status', 20)->default('active');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('gallery_albums')
        );
    }
};
