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
        Schema::create('tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->text('token');
            $table->string('scope');
            $table->string('type');
            $table->uuid('tokenable_id');
            $table->string('tokenable_type');

            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at');

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
        Schema::dropIfExists('tokens');
    }
};
