<?php

use App\Enums\Types\WalletAction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallet_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('wallet_id')
                ->constrained('wallets');

            $table->enum('action', WalletAction::getDBCompatibleEnum());

            $table->decimal('balance_before', 20);
            $table->decimal('amount', 20);
            $table->decimal('balance_after', 20);
            $table->string('narration');

            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_histories');
    }
};
