<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRequest extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'employee_id',
		'subject',
		'application_body',
		'leave_type',
		'start_date',
		'end_date',
		'reason',
		'status',
		'approved_by',
	];

	protected $casts = [
		'start_date' => 'date',
		'end_date' => 'date'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class);
	}

	public function approver()
	{
		return $this->belongsTo(User::class, 'approved_by');
	}
}

