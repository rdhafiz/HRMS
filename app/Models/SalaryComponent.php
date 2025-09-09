<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryComponent extends Model
{
	use HasFactory;

	protected $fillable = [
		'salary_structure_id', 'type', 'name', 'amount',
	];

	protected $casts = [
		'amount' => 'decimal:2',
	];

	public function structure()
	{
		return $this->belongsTo(SalaryStructure::class, 'salary_structure_id');
	}
}


