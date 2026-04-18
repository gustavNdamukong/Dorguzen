<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('seo_global', function (Blueprint $table) {
            $table->id('seo_global_id');

            // Geographic metadata
            $table->string('seo_global_geo_placename', 100)->nullable();
            $table->string('seo_global_geo_region',     10)->nullable();
            $table->string('seo_global_geo_position',   50)->nullable();

            // Hreflang alternate language URLs
            $table->string('seo_global_reflang_alternate1', 255)->nullable();
            $table->string('seo_global_reflang_alternate2', 255)->nullable();

            // Open Graph global tags
            $table->string('seo_global_og_locale',            20)->nullable();
            $table->string('seo_global_og_site',             100)->nullable();
            $table->string('seo_global_og_article_publisher', 255)->nullable();
            $table->string('seo_global_og_author',            255)->nullable();
            $table->string('seo_global_fb_id',                50)->nullable();

            // Twitter global tags
            $table->string('seo_global_twitter_card',  50)->nullable();
            $table->string('seo_global_twitter_site', 255)->nullable();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('seo_global')
        );
    }
};
