<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'first_name',
		'last_name',
		'email',
		'phone',
		'date_of_birth',
		'gender',
		'address',
		'employee_code',
		'join_date',
		'department_id',
		'designation_id',
		'status',
		'created_by',
	];

	protected $casts = [
		'date_of_birth' => 'date',
		'join_date' => 'date',
	];

	public function department()
	{
		return $this->belongsTo(Department::class);
	}

	public function designation()
	{
		return $this->belongsTo(Designation::class);
	}

	public function creator()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function salaryStructures()
	{
		return $this->hasMany(EmployeeSalaryStructure::class);
	}

	public function currentSalaryStructure()
	{
		return $this->hasOne(EmployeeSalaryStructure::class)->latest('effective_date');
	}

	public function getNameAttribute(): string
	{
		return trim($this->first_name . ' ' . $this->last_name);
	}
}