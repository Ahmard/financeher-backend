<?php

use App\Enums\Types\LogTrailActionType;
use App\Enums\Types\LogTrailEntityType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('log_trails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users');

            $table->string('entity_id');

            $table->enum('entity_type', LogTrailEntityType::getDBCompatibleEnum());
            $table->enum('entity_sub_type', LogTrailEntityType::getDBCompatibleEnum())->nullable();
            $table->enum('action', LogTrailActionType::getDBCompatibleEnum());

            $table->ipAddress();
            $table->json('old_data');
            $table->string('desc');
            $table->string('reason')->nullable();

            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_trails');
    }
};
