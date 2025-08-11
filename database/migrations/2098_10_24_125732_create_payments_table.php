<?php

use App\Enums\Statuses\PaymentStatus;
use App\Enums\Types\PaymentGateway;
use App\Enums\Types\PaymentMethod;
use App\Enums\Types\PaymentPurpose;
use App\Enums\Types\PaymentVerificationMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignId('payer_id')
                ->constrained('users');

            $table->foreignId('captured_by')
                ->nullable()
                ->constrained('users');

            $table->foreignId('deleted_by')
                ->nullable()
                ->constrained('users');

            $table->string('reference')->unique()->nullable();
            $table->string('gateway_reference')->unique()->nullable();

            $table->decimal('amount', 20, 4);
            $table->decimal('paid_amount', 20, 4)->nullable();
            $table->decimal('charges', 20, 4)->nullable();
            $table->decimal('computed_amount', 20, 4)->nullable();

            $table->boolean('is_manual_capture')->default(false);
            $table->boolean('is_direct_transfer')->default(false);

            $table->string('checkout_url')->nullable();

            $table->json('init_response')->nullable();
            $table->json('webhook_event')->nullable();
            $table->json('metadata')->nullable();

            $table->string('ip_address', 400);
            $table->string('user_agent', 500);

            $table->enum('gateway', PaymentGateway::getDBCompatibleEnum())
                ->default(PaymentGateway::SQUAD->lowercase());

            $table->enum('verification_method', PaymentVerificationMethod::getDBCompatibleEnum())
                ->default(PaymentVerificationMethod::POLLING->lowercase());

            $table->enum('method', PaymentMethod::getDBCompatibleEnum())
                ->default(PaymentMethod::ONLINE->lowercase());

            $table->enum('purpose', PaymentPurpose::getDBCompatibleEnum())
                ->default(PaymentPurpose::WALLET_FUNDING->lowercase());

            $table->enum('status', PaymentStatus::getDBCompatibleEnum())
                ->default(PaymentStatus::PENDING->lowercase());

            $table->timestamp('paid_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
