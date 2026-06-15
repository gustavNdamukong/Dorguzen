<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('newsletters', function (Blueprint $table) {
            $table->id('newsletter_id');
            $table->string('newsletter_subject', 255);
            $table->text('newsletter_body');
            $table->string('newsletter_image', 500)->nullable();
            $table->string('newsletter_template', 100)->nullable();
            $table->timestamp('newsletter_created')->useCurrent();
            $table->timestamp('newsletter_updated')->useCurrent()->useCurrentOnUpdate();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('newsletters')
        );
    }
};
