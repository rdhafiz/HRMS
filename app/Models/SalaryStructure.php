<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryStructure extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'name', 'description', 'is_template', 'created_by',
	];

	protected $casts = [
		'is_template' => 'boolean',
	];

	public function components()
	{
		return $this->hasMany(SalaryComponent::class);
	}

	public function creator()
	{
		return $this->belongsTo(User::class, 'created_by');
	}
}


