<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('employee_salary_structures', function (Blueprint $table) {
			$table->id();
			$table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
			$table->foreignId('salary_structure_id')->constrained('salary_structures')->cascadeOnDelete();
			$table->json('custom_json')->nullable();
			$table->date('effective_date');
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('employee_salary_structures');
	}
};


