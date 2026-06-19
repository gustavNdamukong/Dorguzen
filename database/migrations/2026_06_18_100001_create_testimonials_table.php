<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('testimonials', function (Blueprint $table) {
            $table->id('testimonial_id');
            $table->string('testimonial_name', 200);
            $table->string('testimonial_company', 200)->nullable();
            $table->string('testimonial_role', 100)->nullable();
            $table->string('testimonial_email', 200)->nullable();
            $table->integer('testimonial_rating')->default(5);
            $table->text('testimonial_comment');
            $table->string('testimonial_status', 20)->default('pending');
            $table->timestamp('created_at')->useCurrent();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('testimonials')
        );
    }
};
