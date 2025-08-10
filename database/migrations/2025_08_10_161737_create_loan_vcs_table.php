<?php

use App\Enums\Statuses\LoanVcStatus;
use App\Enums\Types\LoanVcKind;
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
        Schema::create('loan_vcs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignId('created_by')
                ->constrained('users');

            $table->foreignUuid('business_type_id')
                ->constrained('business_types');

            $table->foreignUuid('opportunity_type_id')
                ->constrained('opportunity_types');

            $table->string('organisation');
            $table->string('currency', 3)->default('USD');
            $table->decimal('lower_amount', 20, 4);
            $table->decimal('upper_amount', 20, 4);

            $table->string('logo');
            $table->string('application_url');
            $table->text('description');

            $table->date('closing_at');

            $table->enum('kind', [LoanVcKind::getDBCompatibleEnum()]);

            $table->enum('status', [LoanVcStatus::getDBCompatibleEnum()])
                ->default(LoanVcStatus::ONGOING->lowercase());

            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_vcs');
    }
};
