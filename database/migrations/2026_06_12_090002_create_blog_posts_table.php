<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('blog_posts', function (Blueprint $table) {
            $table->id('post_id');
            $table->integer('category_id')->nullable();
            $table->string('title', 255)->notNullable();
            $table->string('slug', 255)->notNullable()->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body')->notNullable();
            $table->string('cover_image', 255)->nullable();
            $table->string('author', 150)->notNullable()->default('Admin');
            $table->string('status', 20)->notNullable()->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('blog_posts')
        );
    }
};
