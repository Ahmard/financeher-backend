<?php

use App\Enums\Types\BillingCycleKind;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignId('created_by')
                ->constrained('users');

            $table->string('name');
            $table->decimal('price', 20, 4);
            $table->enum('billing_cycle', BillingCycleKind::getDBCompatibleEnum());
            $table->jsonb('features');

            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
