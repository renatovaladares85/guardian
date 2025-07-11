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
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_id')->nullable()->after('phone');
            $table->date('hire_date')->nullable()->after('employee_id');
            $table->decimal('salary', 10, 2)->nullable()->after('hire_date');
            
            $table->index('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['employee_id']);
            $table->dropColumn(['employee_id', 'hire_date', 'salary']);
        });
    }
};
