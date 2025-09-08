<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('holidays', function (Blueprint $table) {
			$table->id();
			$table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
			$table->string('name');
			$table->date('start_date');
			$table->date('end_date');
			$table->text('description')->nullable();
			$table->softDeletes();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('holidays');
	}
};

