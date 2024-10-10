<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Shared\Models\Migrations\BlamableMigrationTrait;

return new class extends Migration
{
    use BlamableMigrationTrait;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_options', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->json('name');
            $table->string('slug');
            $table->json('description')->nullable();
            $table->string('type'); // option, supplement, gift etc

            $table->integer('min')->default(1); // min number of items that need to be chosen
            $table->integer('max')->default(1); // max number of items that can to be chosen

            $table->boolean('is_active')->default(true);

            // Blamable
            $this->runBlamable($table);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_options');
    }
};
