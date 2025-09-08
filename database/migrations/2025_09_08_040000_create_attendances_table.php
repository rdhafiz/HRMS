<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('attendances', function (Blueprint $table) {
			$table->id();
			$table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
			$table->date('date');
			$table->dateTime('check_in')->nullable();
			$table->dateTime('check_out')->nullable();
			$table->enum('status', ['present','absent','late','half_day','leave','holiday'])->default('present');
			$table->decimal('working_hours', 5, 2)->default(0);
			$table->text('remarks')->nullable();
			$table->softDeletes();
			$table->timestamps();
			$table->unique(['employee_id','date']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('attendances');
	}
};

