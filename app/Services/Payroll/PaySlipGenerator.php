<?php

namespace App\Services\Payroll;

use App\Models\Employee;
use App\Models\SalaryStructure;
use App\Models\PaySlip;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class PaySlipGenerator
{
    /**
     * Generate pay slip data for an employee.
     *
     * @param Employee $employee
     * @param SalaryStructure $salaryStructure
     * @param array $overrides
     * @return array
     * @throws InvalidArgumentException
     */
    public function generatePaySlipData(Employee $employee, SalaryStructure $salaryStructure, array $overrides = []): array
    {
        // Validate employee has salary structure
        if (!$salaryStructure) {
            throw new InvalidArgumentException("Employee {$employee->id} does not have an assigned salary structure.");
        }

        // Get basic salary (with override if provided)
        $basicComponent = 0;
        foreach ($salaryStructure->components as $component) {
            if ($component->type === 'basic') {
                $basicComponent = $component->amount;
            }
        }
        $basic = $overrides['basic'] ?? $basicComponent;

        // Get allowances (merge structure allowances with overrides)
        $structureAllowances = $salaryStructure->components()
            ->where('type', 'allowance')
            ->pluck('amount', 'name')
            ->toArray();
        $allowances = array_merge($structureAllowances, $overrides['allowances'] ?? []);

        // Get deductions (merge structure deductions with overrides)
        $structureDeductions = $salaryStructure->components()
            ->where('type', 'deduction')
            ->pluck('amount', 'name')
            ->toArray();
        $deductions = array_merge($structureDeductions, $overrides['deductions'] ?? []);

        // Calculate gross salary
        $grossSalary = $basic + array_sum($allowances);

        // Calculate net salary
        $netSalary = $grossSalary - array_sum($deductions);

        // Prevent negative net salary
        if ($netSalary < 0) {
            Log::warning("Pay slip for employee {$employee->id} would result in negative net salary: {$netSalary}");
            $netSalary = 0;
        }

        return [
            'basic' => round($basic, 2),
            'allowances' => array_map(fn($amount) => round($amount, 2), $allowances),
            'deductions' => array_map(fn($amount) => round($amount, 2), $deductions),
            'gross_salary' => round($grossSalary, 2),
            'net_salary' => round($netSalary, 2),
        ];
    }

    /**
     * Generate pay slip for a specific period.
     *
     * @param Employee $employee
     * @param string $periodType
     * @param array $periodData
     * @param array $overrides
     * @param int $generatedBy
     * @return PaySlip
     * @throws InvalidArgumentException
     */
    public function generatePaySlip(
        Employee $employee,
        string $periodType,
        array $periodData,
        array $overrides = [],
        int $generatedBy
    ): PaySlip {
        // Get current salary structure for the employee
        $salaryStructure = $employee->currentSalaryStructure?->structure;

        if (!$salaryStructure) {
            throw new InvalidArgumentException("Employee {$employee->id} does not have an assigned salary structure.");
        }

        // Generate pay slip data
        $paySlipData = $this->generatePaySlipData($employee, $salaryStructure, $overrides);

        // Prepare period data
        $periodFields = $this->preparePeriodFields($periodType, $periodData);

        // Create pay slip record
        $paySlip = PaySlip::create([
            'employee_id' => $employee->id,
            'salary_structure_id' => $salaryStructure->id,
            'period_type' => $periodType,
            ...$periodFields,
            ...$paySlipData,
            'status' => 'Pending',
            'generated_at' => now(),
            'generated_by' => $generatedBy,
            'meta' => [
                'overrides' => $overrides,
                'generated_at' => now()->toISOString(),
            ],
        ]);

        Log::info("Pay slip generated for employee {$employee->id}, period: {$paySlip->period_label}");

        return $paySlip;
    }

    /**
     * Generate pay slips for multiple employees.
     *
     * @param array $employeeIds
     * @param string $periodType
     * @param array $periodData
     * @param array $overrides
     * @param int $generatedBy
     * @return array
     */
    public function generateBatchPaySlips(
        array $employeeIds,
        string $periodType,
        array $periodData,
        array $overrides = [],
        int $generatedBy
    ): array {
        $results = [
            'success' => [],
            'errors' => [],
        ];

        foreach ($employeeIds as $employeeId) {
            try {
                $employee = Employee::with('currentSalaryStructure.structure')->find($employeeId);
                
                if (!$employee) {
                    $results['errors'][] = "Employee {$employeeId} not found.";
                    continue;
                }

                $paySlip = $this->generatePaySlip($employee, $periodType, $periodData, $overrides, $generatedBy);
                $results['success'][] = $paySlip;

            } catch (\Exception $e) {
                $results['errors'][] = "Employee {$employeeId}: " . $e->getMessage();
                Log::error("Failed to generate pay slip for employee {$employeeId}: " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Prepare period fields based on period type.
     *
     * @param string $periodType
     * @param array $periodData
     * @return array
     */
    private function preparePeriodFields(string $periodType, array $periodData): array
    {
        if ($periodType === 'month') {
            return [
                'period_month' => $periodData['month'],
                'period_year' => $periodData['year'],
                'period_week_start' => null,
                'period_week_end' => null,
            ];
        } else {
            return [
                'period_month' => null,
                'period_year' => null,
                'period_week_start' => $periodData['week_start'],
                'period_week_end' => $periodData['week_end'],
            ];
        }
    }

    /**
     * Validate period data.
     *
     * @param string $periodType
     * @param array $periodData
     * @return bool
     * @throws InvalidArgumentException
     */
    public function validatePeriodData(string $periodType, array $periodData): bool
    {
        if ($periodType === 'month') {
            if (!isset($periodData['month']) || !isset($periodData['year'])) {
                throw new InvalidArgumentException('Month and year are required for monthly period.');
            }
            if ($periodData['month'] < 1 || $periodData['month'] > 12) {
                throw new InvalidArgumentException('Month must be between 1 and 12.');
            }
            if ($periodData['year'] < 2020 || $periodData['year'] > 2030) {
                throw new InvalidArgumentException('Year must be between 2020 and 2030.');
            }
        } else {
            if (!isset($periodData['week_start']) || !isset($periodData['week_end'])) {
                throw new InvalidArgumentException('Week start and end dates are required for weekly period.');
            }
            if (strtotime($periodData['week_start']) >= strtotime($periodData['week_end'])) {
                throw new InvalidArgumentException('Week start date must be before week end date.');
            }
        }

        return true;
    }

    /**
     * Check if pay slip already exists for the period.
     *
     * @param int $employeeId
     * @param string $periodType
     * @param array $periodData
     * @return bool
     */
    public function paySlipExists(int $employeeId, string $periodType, array $periodData): bool
    {
        $query = PaySlip::where('employee_id', $employeeId)
                       ->where('period_type', $periodType);

        if ($periodType === 'month') {
            $query->where('period_month', $periodData['month'])
                  ->where('period_year', $periodData['year']);
        } else {
            $query->where('period_week_start', $periodData['week_start'])
                  ->where('period_week_end', $periodData['week_end']);
        }

        return $query->exists();
    }
}
