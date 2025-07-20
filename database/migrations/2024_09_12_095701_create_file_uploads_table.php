<?php

use App\Enums\Entity;
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
        Schema::create('file_uploads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users');

            $table->enum('entity_type', Entity::getDBCompatibleEnum());
            $table->string('entity_id');
            $table->string('desc', 250)->nullable();
            $table->string('additional_info', 250)->nullable();
            $table->string('orig_name');
            $table->string('file_path');
            $table->string('file_ext');

            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_uploads');
    }
};
