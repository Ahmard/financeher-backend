<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('geo_states', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('country_id')
                ->constrained('geo_countries');

            $table->string('name');
            $table->string('code');

            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_states');
    }
};
