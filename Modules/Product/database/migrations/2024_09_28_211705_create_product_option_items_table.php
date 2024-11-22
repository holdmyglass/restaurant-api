<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
        Schema::create('product_option_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->json('name');
            $table->string('slug');
            $table->json('description')->nullable();
            $table->integer('rank');
            $table->integer('min')->default(0); // min number of items that need to be chosen per option
            $table->integer('max')->default(1); // max number of this item that can to be chosen per option

            // availability
            $table->boolean('available')->default(true);

            // accounting
            $table->integer('vat')->default(600); // 6%

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
        Schema::dropIfExists('product_option_items');
    }
};
