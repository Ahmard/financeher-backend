<?php

use App\Enums\Types\PaymentGateway;
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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('created_by')
                ->constrained('users');

            $table->string('app_version')->default('0.0.1');
            $table->boolean('system_status');
            $table->boolean('login_module_status');
            $table->boolean('register_module_status');
            $table->boolean('mail_module_status');
            $table->boolean('wallet_module_status');
            $table->boolean('payment_module_status');

            $table->string('module_maintenance_message', 5000);
            $table->string('system_maintenance_message', 5000);

            $table->json('moniepoint_auth_token')->default(null);

            $table->integer('moniepoint_vat')->default(0.075);
            $table->integer('moniepoint_card_charges')->default(1.50);
            $table->integer('moniepoint_transfer_charges')->default(1.50);
            $table->integer('moniepoint_van_transfer_charges')->default(1.00);

            $table->enum('payment_gateway', PaymentGateway::getDBCompatibleEnum())
                ->default(PaymentGateway::MONNIFY->lowercase());

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
        Schema::dropIfExists('system_settings');
    }
};
