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
        Schema::create('products', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->json('name');
            $table->string('slug');
            $table->json('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('rank')->nullable();

            // availability
            $table->boolean('available')->default(true);
            $table->boolean('takeaway')->default | (true);
            $table->boolean('delivery')->default(true);
            $table->boolean('eat_in')->default(true);
            $table->boolean('offer')->default(false);

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
        Schema::dropIfExists('products');
    }
};
