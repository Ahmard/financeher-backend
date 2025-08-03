<?php

use App\Enums\Statuses\UserStatus;
use App\Enums\Types\UserRegistrationStage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('country_id')
                ->nullable()
                ->constrained('geo_countries');

            $table->foreignId('invited_by')
                ->nullable()
                ->constrained('users');

            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('email')->unique();

            $table->string('business_name')
                ->unique()
                ->nullable();

            $table->string('profile_picture')->nullable();

            $table->enum('registration_stage', UserRegistrationStage::getDBCompatibleEnum())
                ->default(UserRegistrationStage::EMAIL_VERIFICATION->lowercase());

            $table->string('email_verification_code', 10)->nullable();
            $table->string('email_verification_token')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->smallInteger('failed_logins')->default(0);
            $table->boolean('has_password');
            $table->enum('status', UserStatus::getDBCompatibleEnum());

            $table->rememberToken();

            $table->timestampTz('last_login_at')->nullable();
            $table->timestampTz('last_password_reset_at')->nullable();
            $table->timestampTz('suspended_until')->nullable();

            $table->softDeletesTz();
            $table->timestampsTz();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('expires_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
