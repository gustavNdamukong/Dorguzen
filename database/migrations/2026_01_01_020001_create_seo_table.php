<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('seo', function (Blueprint $table) {
            $table->id('seo_id');

            $table->string('seo_page_name', 100)->unique();
            $table->tinyInteger('seo_dynamic')->default(0);

            // Meta tags — EN / FR / ES
            $table->string('seo_meta_title_en',  60)->nullable();
            $table->string('seo_meta_title_fre', 60)->nullable();
            $table->string('seo_meta_title_es',  60)->nullable();

            $table->string('seo_meta_desc_en',  150)->nullable();
            $table->string('seo_meta_desc_fre', 150)->nullable();
            $table->string('seo_meta_desc_es',  150)->nullable();

            $table->string('seo_keywords_en',  200)->nullable();
            $table->string('seo_keywords_fre', 200)->nullable();
            $table->string('seo_keywords_es',  200)->nullable();

            // Headings — EN / FR / ES
            $table->string('seo_h1_text_en',  70)->nullable();
            $table->string('seo_h1_text_fre', 70)->nullable();
            $table->string('seo_h1_text_es',  70)->nullable();

            $table->string('seo_h2_text_en',  70)->nullable();
            $table->string('seo_h2_text_fre', 70)->nullable();
            $table->string('seo_h2_text_es',  70)->nullable();

            // Page content — EN / FR / ES
            $table->text('seo_page_content_en')->nullable();
            $table->text('seo_page_content_fre')->nullable();
            $table->text('seo_page_content_es')->nullable();

            // Technical SEO
            $table->string('seo_canonical_href', 255)->nullable();
            $table->tinyInteger('seo_no_index')->default(0);

            // Open Graph — EN / FR / ES
            $table->string('seo_og_title_en',  100)->nullable();
            $table->string('seo_og_title_fre', 100)->nullable();
            $table->string('seo_og_title_es',  100)->nullable();

            $table->string('seo_og_desc_en',  200)->nullable();
            $table->string('seo_og_desc_fre', 200)->nullable();
            $table->string('seo_og_desc_es',  200)->nullable();

            $table->string('seo_og_type_en',  50)->nullable();
            $table->string('seo_og_type_fre', 50)->nullable();
            $table->string('seo_og_type_es',  50)->nullable();

            $table->string('seo_og_image',            255)->nullable();
            $table->integer('seo_og_image_width')->nullable();
            $table->integer('seo_og_image_height')->nullable();
            $table->string('seo_og_image_secure_url', 255)->nullable();
            $table->string('seo_og_url',              255)->nullable();
            $table->string('seo_og_video',            255)->nullable();

            // Twitter Card — EN / FR / ES
            $table->string('seo_twitter_title_en',  100)->nullable();
            $table->string('seo_twitter_title_fre', 100)->nullable();
            $table->string('seo_twitter_title_es',  100)->nullable();

            $table->string('seo_twitter_desc_en',  200)->nullable();
            $table->string('seo_twitter_desc_fre', 200)->nullable();
            $table->string('seo_twitter_desc_es',  200)->nullable();

            $table->string('seo_twitter_image', 255)->nullable();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('seo')
        );
    }
};
