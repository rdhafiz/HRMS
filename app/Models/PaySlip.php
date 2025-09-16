<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class PaySlip extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'salary_structure_id',
        'period_type',
        'period_month',
        'period_year',
        'period_week_start',
        'period_week_end',
        'basic',
        'allowances',
        'deductions',
        'gross_salary',
        'net_salary',
        'status',
        'generated_at',
        'paid_at',
        'generated_by',
        'pdf_path',
        'meta',
    ];

    protected $casts = [
        'allowances' => 'array',
        'deductions' => 'array',
        'meta' => 'array',
        'generated_at' => 'datetime',
        'paid_at' => 'datetime',
        'period_week_start' => 'date',
        'period_week_end' => 'date',
        'basic' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    protected $appends = [
        'total_allowances',
        'total_deductions'
    ];

    /**
     * Get the employee that owns the pay slip.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the salary structure used for this pay slip.
     */
    public function salaryStructure(): BelongsTo
    {
        return $this->belongsTo(SalaryStructure::class);
    }

    /**
     * Get the user who generated this pay slip.
     */
    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Check if the pay slip is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'Paid';
    }

    /**
     * Get the period label for display.
     */
    public function getPeriodLabelAttribute(): string
    {
        if ($this->period_type === 'month') {
            $monthName = Carbon::createFromFormat('m', $this->period_month)->format('F');
            return "{$monthName} {$this->period_year}";
        } else {
            $start = Carbon::parse($this->period_week_start)->format('M j');
            $end = Carbon::parse($this->period_week_end)->format('M j, Y');
            return "Week of {$start} - {$end}";
        }
    }

    /**
     * Get the total allowances amount.
     */
    public function getTotalAllowancesAttribute(): float
    {
        if (!$this->allowances) {
            return 0;
        }
        return array_sum($this->allowances);
    }

    /**
     * Get the total deductions amount.
     */
    public function getTotalDeductionsAttribute(): float
    {
        if (!$this->deductions) {
            return 0;
        }
        return array_sum($this->deductions);
    }

    /**
     * Get formatted currency amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format($this->net_salary, 2);
    }

    /**
     * Mark the pay slip as paid.
     */
    public function markAsPaid(): bool
    {
        return $this->update([
            'status' => 'Paid',
            'paid_at' => now(),
        ]);
    }

    /**
     * Get the PDF file path for download.
     */
    public function getPdfDownloadPathAttribute(): string
    {
        if (!$this->pdf_path) {
            return '';
        }
        return storage_path('app/' . $this->pdf_path);
    }

    /**
     * Check if PDF exists.
     */
    public function hasPdf(): bool
    {
        return $this->pdf_path && file_exists($this->getPdfDownloadPathAttribute());
    }

    /**
     * Scope for filtering by period.
     */
    public function scopeForPeriod($query, $type, $month = null, $year = null, $weekStart = null, $weekEnd = null)
    {
        if ($type === 'month') {
            return $query->where('period_type', 'month')
                        ->where('period_month', $month)
                        ->where('period_year', $year);
        } else {
            return $query->where('period_type', 'week')
                        ->where('period_week_start', $weekStart)
                        ->where('period_week_end', $weekEnd);
        }
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by employee.
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }
}