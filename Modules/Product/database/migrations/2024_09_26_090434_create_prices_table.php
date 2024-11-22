<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Product\Enums\PriceTypeEnum;
use Modules\Shared\Enums\CurrencyEnum;
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
        Schema::create('prices', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->uuidMorphs('priceable');

            $table->string('currency')->default(CurrencyEnum::USD);
            $table->string('name')->default(ucfirst(strtolower(PriceTypeEnum::REGULAR->value)).' Price');
            $table->integer('price');
            $table->string('price_type')->default(PriceTypeEnum::REGULAR);
            $table->dateTime('valid_from')->nullable()->index();
            $table->dateTime('valid_until')->nullable()->index();

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
        Schema::dropIfExists('prices');
    }
};
