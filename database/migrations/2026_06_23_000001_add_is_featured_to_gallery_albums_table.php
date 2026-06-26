<?php

use Dorguzen\Core\Database\Migrations\Migration;

/**
 * Adds a "featured" flag to gallery albums. The album flagged as featured
 * (only one at a time) powers the homepage image slider.
 */
return new class extends Migration {

    public function up(): void
    {
        $this->addStatement(
            "ALTER TABLE gallery_albums
                ADD COLUMN album_is_featured TINYINT(1) NOT NULL DEFAULT 0 AFTER album_status"
        );
    }

    public function down(): void
    {
        $this->addStatement(
            "ALTER TABLE gallery_albums DROP COLUMN album_is_featured"
        );
    }
};
