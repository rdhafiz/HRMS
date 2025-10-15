<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeTrainingPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_user_id',
        'employee_id',
        'training_policy_id',
    ];

    /**
     * Get the employee user that owns the training policy completion.
     */
    public function employeeUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_user_id');
    }

    /**
     * Get the employee that owns the training policy completion.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the training policy that was completed.
     */
    public function trainingPolicy(): BelongsTo
    {
        return $this->belongsTo(TrainingAndPolicy::class, 'training_policy_id');
    }
}
