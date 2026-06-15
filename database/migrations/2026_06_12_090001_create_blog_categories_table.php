<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('blog_categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('name', 150)->notNullable();
            $table->string('slug', 150)->notNullable()->unique();
            $table->timestamp('created_at')->useCurrent();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('blog_categories')
        );
    }
};
