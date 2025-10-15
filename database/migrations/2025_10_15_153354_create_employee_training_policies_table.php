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
        Schema::create('employee_training_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('training_policy_id')->constrained('trainings_and_policies')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure unique combination of employee and training policy
            $table->unique(['employee_user_id', 'training_policy_id'], 'unique_employee_training_policy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_training_policies');
    }
};
