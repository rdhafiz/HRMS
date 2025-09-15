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
        Schema::create('pay_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('salary_structure_id')->nullable()->constrained('salary_structures')->onDelete('set null');
            $table->enum('period_type', ['month', 'week']);
            $table->tinyInteger('period_month')->nullable(); // 1-12 for monthly
            $table->smallInteger('period_year')->nullable();
            $table->date('period_week_start')->nullable(); // for weekly
            $table->date('period_week_end')->nullable(); // for weekly
            $table->decimal('basic', 12, 2);
            $table->json('allowances')->nullable(); // {"House Rent": 10000, "Transport": 2000}
            $table->json('deductions')->nullable(); // {"Tax": 1500, "Insurance": 500}
            $table->decimal('gross_salary', 12, 2);
            $table->decimal('net_salary', 12, 2);
            $table->enum('status', ['Pending', 'Paid'])->default('Pending');
            $table->timestamp('generated_at');
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade');
            $table->string('pdf_path')->nullable();
            $table->json('meta')->nullable(); // extra data, reason for regenerate, etc.
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index('employee_id');
            $table->index(['period_type', 'period_month', 'period_year']);
            $table->index(['period_type', 'period_week_start', 'period_week_end']);
            $table->index('status');
            $table->index('generated_at');

            // Unique constraint to prevent duplicates (unless re-generation is allowed)
            $table->unique(['employee_id', 'period_type', 'period_month', 'period_year', 'period_week_start'], 'unique_monthly_period');
            $table->unique(['employee_id', 'period_type', 'period_week_start', 'period_week_end'], 'unique_weekly_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pay_slips');
    }
};
