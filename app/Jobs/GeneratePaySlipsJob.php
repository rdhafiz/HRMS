<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\UserLog;
use App\Services\Payroll\PaySlipGenerator;
use App\Services\Payroll\PdfGenerator;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GeneratePaySlipsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    protected array $employeeIds;
    protected string $periodType;
    protected array $periodData;
    protected array $overrides;
    protected int $generatedBy;
    protected bool $generatePdf;

    /**
     * Create a new job instance.
     */
    public function __construct(
        array $employeeIds,
        string $periodType,
        array $periodData,
        array $overrides = [],
        int $generatedBy,
        bool $generatePdf = true
    ) {
        $this->employeeIds = $employeeIds;
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
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        $generator = new PaySlipGenerator();
        $results = $generator->generateBatchPaySlips(
            $this->employeeIds,
            $this->periodType,
            $this->periodData,
            $this->overrides,
            $this->generatedBy
        );

        // Generate PDFs if requested
        if ($this->generatePdf) {
            foreach ($results['success'] as $paySlip) {
                try {
                    $this->generatePdfForPaySlip($paySlip);
                } catch (\Exception $e) {
                    Log::error("Failed to generate PDF for pay slip {$paySlip->id}: " . $e->getMessage());
                }
            }
        }

        // Log the batch generation
        UserLog::create([
            'user_id' => $this->generatedBy,
            'type' => 'pay_slip_batch_generated',
            'metadata' => [
                'period_type' => $this->periodType,
                'period_data' => $this->periodData,
                'success_count' => count($results['success']),
                'error_count' => count($results['errors']),
                'errors' => $results['errors'],
            ],
        ]);

        Log::info("Pay slip batch generation completed", [
            'success_count' => count($results['success']),
            'error_count' => count($results['errors']),
        ]);
    }

    /**
     * Generate PDF for a pay slip.
     */
    private function generatePdfForPaySlip($paySlip): void
    {
        $pdfGenerator = new PdfGenerator();
        $pdfGenerator->generatePaySlipPdf($paySlip);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Pay slip batch generation failed", [
            'employee_ids' => $this->employeeIds,
            'period_type' => $this->periodType,
            'period_data' => $this->periodData,
            'error' => $exception->getMessage(),
        ]);

        UserLog::create([
            'user_id' => $this->generatedBy,
            'type' => 'pay_slip_batch_failed',
            'metadata' => [
                'employee_ids' => $this->employeeIds,
                'period_type' => $this->periodType,
                'period_data' => $this->periodData,
                'error' => $exception->getMessage(),
            ],
        ]);
    }
}