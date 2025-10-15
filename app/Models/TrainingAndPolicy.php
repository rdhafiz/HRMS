<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingAndPolicy extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'trainings_and_policies';

    protected $fillable = [
        'title',
        'type',
        'description',
        'category_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the category that owns the training/policy.
     */
    public function category()
    {
        return $this->belongsTo(TrainingPolicyCategory::class, 'category_id');
    }
}
