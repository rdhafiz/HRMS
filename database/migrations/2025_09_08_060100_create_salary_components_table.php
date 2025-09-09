<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('salary_components', function (Blueprint $table) {
			$table->id();
			$table->foreignId('salary_structure_id')->constrained('salary_structures')->cascadeOnDelete();
			$table->enum('type', ['basic','allowance','deduction']);
			$table->string('name');
			$table->decimal('amount', 12, 2);
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('salary_components');
	}
};


