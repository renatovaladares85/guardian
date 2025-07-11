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
        Schema::create('project_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['project_manager', 'team_lead', 'team_member', 'observer'])
                  ->default('team_member');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            // Unique constraint to prevent duplicate memberships
            $table->unique(['project_id', 'user_id']);
            
            // Indexes
            $table->index('project_id');
            $table->index('user_id');
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_users');
    }
};
