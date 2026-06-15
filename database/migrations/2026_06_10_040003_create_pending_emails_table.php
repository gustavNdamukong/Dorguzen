<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('pending_emails', function (Blueprint $table) {
            $table->id('id');
            $table->integer('subscriber_id');
            $table->string('subscriber_email', 255);
            $table->string('subscriber_name', 100)->default('');
            $table->integer('newsletter_id');
            $table->string('newsletter_subject', 255);
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->integer('tries')->default(0);
            $table->dateTime('last_attempt_at')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('pending_emails')
        );
    }
};
