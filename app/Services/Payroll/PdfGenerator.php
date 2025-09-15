<?php

namespace App\Services\Payroll;

use App\Models\PaySlip;
use App\Models\Branding;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PdfGenerator
{
    /**
     * Generate PDF for a pay slip.
     *
     * @param PaySlip $paySlip
     * @return string|null Path to generated PDF
     */
    public function generatePaySlipPdf(PaySlip $paySlip): ?string
    {
        try {
            // Load branding information
            $branding = Branding::first();

            // Generate PDF
            $pdf = PDF::loadView('payroll.payslip', [
                'paySlip' => $paySlip->load(['employee.department', 'employee.designation', 'generator']),
                'branding' => $branding,
            ]);

            // Configure PDF options
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial',
            ]);

            // Generate file path
            $year = $paySlip->generated_at->year;
            $month = $paySlip->generated_at->format('m');
            $fileName = "payslip_{$paySlip->employee_id}_{$paySlip->id}_{$paySlip->generated_at->format('Y_m_d')}.pdf";
            $filePath = "pay_slips/{$year}/{$month}/{$fileName}";

            // Ensure directory exists
            $fullPath = storage_path("app/{$filePath}");
            $directory = dirname($fullPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Save PDF
            $pdf->save($fullPath);

            // Update pay slip with PDF path
            $paySlip->update(['pdf_path' => $filePath]);

            Log::info("PDF generated for pay slip {$paySlip->id}", [
                'file_path' => $filePath,
                'employee_id' => $paySlip->employee_id,
            ]);

            return $filePath;

        } catch (\Exception $e) {
            Log::error("Failed to generate PDF for pay slip {$paySlip->id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate PDF for multiple pay slips.
     *
     * @param array $paySlips
     * @return array Results with success/error counts
     */
    public function generateBatchPdfs(array $paySlips): array
    {
        $results = [
            'success' => 0,
            'errors' => 0,
            'generated_paths' => [],
        ];

        foreach ($paySlips as $paySlip) {
            try {
                $path = $this->generatePaySlipPdf($paySlip);
                if ($path) {
                    $results['success']++;
                    $results['generated_paths'][] = $path;
                } else {
                    $results['errors']++;
                }
            } catch (\Exception $e) {
                $results['errors']++;
                Log::error("Failed to generate PDF for pay slip {$paySlip->id}: " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Get PDF content for download.
     *
     * @param PaySlip $paySlip
     * @return string|null PDF content
     */
    public function getPaySlipPdfContent(PaySlip $paySlip): ?string
    {
        if (!$paySlip->pdf_path || !Storage::exists($paySlip->pdf_path)) {
            // Generate PDF if it doesn't exist
            $this->generatePaySlipPdf($paySlip);
        }

        if ($paySlip->pdf_path && Storage::exists($paySlip->pdf_path)) {
            return Storage::get($paySlip->pdf_path);
        }

        return null;
    }

    /**
     * Delete PDF file.
     *
     * @param PaySlip $paySlip
     * @return bool
     */
    public function deletePaySlipPdf(PaySlip $paySlip): bool
    {
        if ($paySlip->pdf_path && Storage::exists($paySlip->pdf_path)) {
            Storage::delete($paySlip->pdf_path);
            $paySlip->update(['pdf_path' => null]);
            return true;
        }

        return false;
    }

    /**
     * Regenerate PDF for a pay slip.
     *
     * @param PaySlip $paySlip
     * @return string|null New PDF path
     */
    public function regeneratePaySlipPdf(PaySlip $paySlip): ?string
    {
        // Delete existing PDF
        $this->deletePaySlipPdf($paySlip);

        // Generate new PDF
        return $this->generatePaySlipPdf($paySlip);
    }
}
