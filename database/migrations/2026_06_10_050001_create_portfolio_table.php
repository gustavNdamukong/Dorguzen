<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        $sql = $this->schema->create('portfolio', function (Blueprint $table) {
            $table->id('portfolio_id');
            $table->string('portfolio_title', 255);
            $table->string('portfolio_company_name', 255)->nullable();
            $table->string('portfolio_website', 500)->nullable();
            $table->text('portfolio_description')->nullable();
            $table->string('portfolio_image', 500)->nullable();
            $table->timestamp('portfolio_created')->useCurrent();
            $table->timestamp('portfolio_updated')->useCurrent()->useCurrentOnUpdate();
        });

        $this->addStatement($sql);
    }

    public function down(): void
    {
        $this->addStatement(
            $this->schema->dropIfExists('portfolio')
        );
    }
};
