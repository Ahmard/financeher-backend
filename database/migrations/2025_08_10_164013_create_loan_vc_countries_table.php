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
        Schema::create('loan_vc_countries', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignId('created_by')
                ->constrained('users');

            $table->foreignUuid('loan_vc_id')
                ->constrained('loan_vcs');

            $table->foreignUuid('country_id')
                ->constrained('geo_countries');

            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_vc_countries');
    }
};
