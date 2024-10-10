<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Product\Enums\ProductCategoryTypeEnum;
use Modules\Shared\Models\Migrations\BlamableMigrationTrait;
use Modules\Shared\Models\Migrations\VersionableTraitMigration;

return new class extends Migration
{
    use BlamableMigrationTrait, VersionableTraitMigration;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->uuid('parent_id')->nullable()->constrained('product_categories', 'parent_id', 'id')->onDelete('cascade');

            $table->json('name');
            $table->string('slug');
            $table->json('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('rank')->nullable();

            $table->string('type')->default(ProductCategoryTypeEnum::DISH);

            // Blamable
            $this->runBlamable($table);

            // Versionable
            $this->runVersionable($table);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
