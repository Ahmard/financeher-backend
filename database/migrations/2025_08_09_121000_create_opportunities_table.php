<?php

use App\Enums\Statuses\OpportunityStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignId('created_by')
                ->constrained('users');

            $table->foreignUuid('country_id')
                ->constrained('geo_countries');

            $table->foreignUuid('industry_id')
                ->constrained('industries');

            $table->foreignUuid('opportunity_type_id')
                ->constrained('opportunity_types');

            $table->string('name');
            $table->string('organisation');
            $table->string('currency', 3)->default('USD');
            $table->decimal('lower_amount', 20, 4);
            $table->decimal('upper_amount', 20, 4);

            $table->string('logo');
            $table->string('application_url');
            $table->text('overview');

            $table->date('closing_at');

            $table->enum('status', [OpportunityStatus::getDBCompatibleEnum()])
                ->default(OpportunityStatus::ONGOING->lowercase());

            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
