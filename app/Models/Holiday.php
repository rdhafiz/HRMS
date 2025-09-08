<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'department_id',
		'name',
		'start_date',
		'end_date',
		'description',
	];

	protected $casts = [
		'start_date' => 'date',
		'end_date' => 'date',
	];
}

