<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_industries', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignId('user_id')
                ->constrained('users');

            $table->foreignId('created_by')
                ->constrained('users');

            $table->foreignUuid('industry_id')
                ->constrained('industries');

            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_industries');
    }
};
