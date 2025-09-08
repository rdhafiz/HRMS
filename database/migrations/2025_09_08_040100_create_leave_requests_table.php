<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('leave_requests', function (Blueprint $table) {
			$table->id();
			$table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
			$table->enum('leave_type', ['sick','casual','paid','unpaid']);
			$table->date('start_date');
			$table->date('end_date');
			$table->text('reason')->nullable();
			$table->enum('status', ['pending','approved','rejected'])->default('pending');
			$table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
			$table->softDeletes();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('leave_requests');
	}
};

