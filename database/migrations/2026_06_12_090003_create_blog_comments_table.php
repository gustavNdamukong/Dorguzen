<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('blog_comments', function (Blueprint $table) {
            $table->id('comment_id');
            $table->integer('post_id')->notNullable();
            $table->string('author_name', 150)->notNullable();
            $table->string('author_email', 255)->notNullable();
            $table->text('body')->notNullable();
            $table->string('status', 20)->notNullable()->default('pending');
            $table->timestamp('created_at')->useCurrent();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('blog_comments')
        );
    }
};
