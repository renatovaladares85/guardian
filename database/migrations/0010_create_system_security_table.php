<?php

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
        Schema::create('system_security', function (Blueprint $table) {
            $table->id();
            $table->string('machine_fingerprint')->unique();
            $table->string('encryption_key', 64);
            $table->string('app_key', 64);
            $table->json('security_settings');
            $table->boolean('is_setup_complete')->default(false);
            $table->timestamp('setup_completed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('machine_fingerprint');
            $table->index('is_setup_complete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_security');
    }
};
