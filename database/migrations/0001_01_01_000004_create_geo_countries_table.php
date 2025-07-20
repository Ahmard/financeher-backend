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
        Schema::create('geo_countries', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->string('code');
            $table->string('capital');
            $table->string('region');
            $table->string('subregion');

            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_countries');
    }
};
