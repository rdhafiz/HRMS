<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('employees', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();

			// Personal Information
			$table->string('first_name');
			$table->string('last_name');
			$table->string('email')->unique();
			$table->string('phone')->nullable();
			$table->date('date_of_birth')->nullable();
			$table->enum('gender', ['male', 'female', 'other'])->nullable();
			$table->text('address')->nullable();

			// Professional Information
			$table->string('employee_code')->unique();
			$table->date('join_date');
			$table->unsignedInteger('department_id')->default(0);
			$table->unsignedInteger('designation_id')->default(0);
			$table->enum('status', ['active', 'inactive', 'terminated'])->default('active');
			$table->unsignedInteger('salary')->default(0);

			// Metadata
			$table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

			$table->softDeletes();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('employees');
	}
};