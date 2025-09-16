<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\PaySlip;
use App\Models\UserLog;
use App\Services\Payroll\PaySlipGenerator;
use App\Services\Payroll\PdfGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePaySlipForEmployeeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // 2 minutes
    public $tries = 3;

    protected int $employeeId;
    protected string $periodType;
    protected array $periodData;
    protected array $overrides;
    protected int $generatedBy;
    protected bool $generatePdf;

    /**
     * Create a new job instance.
     */
    public function __construct(
        int $employeeId,
        string $periodType,
        array $periodData,
        array $overrides = [],
        int $generatedBy,
        bool $generatePdf = true
    ) {
        $this->employeeId = $employeeId;
        $this->periodType = $periodType;
        $this->periodData = $periodData;
        $this->overrides = $overrides;
        $this->generatedBy = $generatedBy;
        $this->generatePdf = $generatePdf;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $employee = Employee::with('currentSalaryStructure.structure')->find($this->employeeId);
            
            if (!$employee) {
                throw new \Exception("Employee {$this->employeeId} not found.");
            }

            $generator = new PaySlipGenerator();
            $paySlip = $generator->generatePaySlip(
                $employee,
                $this->periodType,
                $this->periodData,
                $this->overrides,
                $this->generatedBy
            );

            // Generate PDF if requested
            if ($this->generatePdf) {
                $this->generatePdfForPaySlip($paySlip);
            }

            // Log the generation
            UserLog::create([
                'user_id' => $this->generatedBy,
                'type' => 'pay_slip_generated',
                'metadata' => [
                    'employee_id' => $this->employeeId,
                    'pay_slip_id' => $paySlip->id,
                    'period_type' => $this->periodType,
                    'period_data' => $this->periodData,
                ],
            ]);

            Log::info("Pay slip generated for employee {$this->employeeId}", [
                'pay_slip_id' => $paySlip->id,
                'period' => $paySlip->period_label,
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to generate pay slip for employee {$this->employeeId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate PDF for a pay slip.
     */
    private function generatePdfForPaySlip(PaySlip $paySlip): void
    {
        $pdfGenerator = new PdfGenerator();
        $pdfGenerator->generatePaySlipPdf($paySlip);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Pay slip generation failed for employee {$this->employeeId}", [
            'period_type' => $this->periodType,
            'period_data' => $this->periodData,
            'error' => $exception->getMessage(),
        ]);

        UserLog::create([
            'user_id' => $this->generatedBy,
            'type' => 'pay_slip_generation_failed',
            'metadata' => [
                'employee_id' => $this->employeeId,
                'period_type' => $this->periodType,
                'period_data' => $this->periodData,
                'error' => $exception->getMessage(),
            ],
        ]);
    }
}