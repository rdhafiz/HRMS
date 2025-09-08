<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'employee_id',
		'date',
		'check_in',
		'check_out',
		'status',
		'working_hours',
		'remarks',
	];

	protected $casts = [
		'date' => 'date',
		'check_in' => 'datetime',
		'check_out' => 'datetime',
		'working_hours' => 'decimal:2',
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class);
	}
}

