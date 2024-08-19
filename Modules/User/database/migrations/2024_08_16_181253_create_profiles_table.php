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
        Schema::create('profiles', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->uuid('user_id');
            $table->string('user_name')->nullable();
            $table->string('full_name')->nullable();
            $table->text('description')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('display_img')->nullable();
            $table->string('cover_img')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('type');

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
        Schema::dropIfExists('profiles');
    }
};
