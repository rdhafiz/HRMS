<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalaryStructure extends Model
{
	use HasFactory;

	protected $fillable = [
		'employee_id', 'salary_structure_id', 'custom_json', 'effective_date',
	];

	protected $casts = [
		'custom_json' => 'array',
		'effective_date' => 'date',
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class);
	}

	public function structure()
	{
		return $this->belongsTo(SalaryStructure::class, 'salary_structure_id');
	}
}


