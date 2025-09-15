<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaySlip;
use App\Models\Employee;
use App\Models\UserLog;
use App\Services\Payroll\PaySlipGenerator;
use App\Services\Payroll\PdfGenerator;
use App\Jobs\GeneratePaySlipsJob;
use App\Jobs\GeneratePaySlipForEmployeeJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PaySlipController extends Controller
{
    protected PaySlipGenerator $paySlipGenerator;

    public function __construct(PaySlipGenerator $paySlipGenerator)
    {
        $this->paySlipGenerator = $paySlipGenerator;
    }

    /**
     * Display a listing of pay slips.
     */
    public function index(Request $request): JsonResponse
    {
        $query = PaySlip::with(['employee.department', 'salaryStructure', 'generator']);

        // Apply filters
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('period_type')) {
            $query->where('period_type', $request->period_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        $paySlips = $query->orderBy('generated_at', 'desc')->paginate(15);

        return response()->json($paySlips);
    }

    /**
     * Store a newly created pay slip (single generation).
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'period_type' => 'required|in:month,week',
            'period_month' => 'required_if:period_type,month|integer|min:1|max:12',
            'period_year' => 'required_if:period_type,month|integer|min:2020|max:2030',
            'period_week_start' => 'required_if:period_type,week|date',
            'period_week_end' => 'required_if:period_type,week|date|after:period_week_start',
            'overrides' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $employee = Employee::with('currentSalaryStructure.structure')->find($request->employee_id);
            
            if (!$employee) {
                return response()->json(['error' => 'Employee not found'], 404);
            }

            $periodData = $this->preparePeriodData($request);

            if ($this->paySlipGenerator->paySlipExists($request->employee_id, $request->period_type, $periodData)) {
                return response()->json(['error' => 'Pay slip already exists for this period'], 409);
            }

            $paySlip = $this->paySlipGenerator->generatePaySlip(
                $employee,
                $request->period_type,
                $periodData,
                $request->overrides ?? [],
                $request->user()->id
            );

            return response()->json($paySlip->load(['employee.department', 'salaryStructure']), 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate pay slips in batch.
     */
    public function generateBatch(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
            'period_type' => 'required|in:month,week',
            'period_month' => 'required_if:period_type,month|integer|min:1|max:12',
            'period_year' => 'required_if:period_type,month|integer|min:2020|max:2030',
            'period_week_start' => 'nullable|required_if:period_type,week|date',
            'period_week_end'   => 'nullable|required_if:period_type,week|date|after:period_week_start',
            'overrides' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $periodData = $this->preparePeriodData($request);
            $employeeIds = $request->employee_ids;

            $job = new GeneratePaySlipsJob(
                $employeeIds,
                $request->period_type,
                $periodData,
                $request->overrides ?? [],
                $request->user()->id,
                true
            );

            dispatch($job);

            return response()->json([
                'message' => 'Pay slip generation queued successfully',
                'employee_count' => count($employeeIds),
            ], 202);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified pay slip.
     */
    public function show(PaySlip $paySlip): JsonResponse
    {
        $paySlip->load(['employee.department', 'salaryStructure', 'generator']);
        return response()->json($paySlip);
    }

    /**
     * Download pay slip PDF.
     */
    public function download(PaySlip $paySlip)
    {
        $pdfGenerator = new PdfGenerator();
        
        // Generate PDF if it doesn't exist
        if (!$paySlip->pdf_path || !Storage::disk('public')->exists($paySlip->pdf_path)) {
            $pdfGenerator->generatePaySlipPdf($paySlip);
            $paySlip->refresh();
        }

        if (!$paySlip->pdf_path || !Storage::disk('public')->exists($paySlip->pdf_path)) {
            return response()->json(['error' => 'PDF not available'], 404);
        }

        // Log download action
        UserLog::create([
            'user_id' => auth()->id(),
            'type' => 'pay_slip_downloaded',
            'metadata' => [
                'pay_slip_id' => $paySlip->id,
                'employee_id' => $paySlip->employee_id,
                'period' => $paySlip->period_label,
            ],
        ]);

        return response()->json(['pdf' => asset('storage/'.$paySlip->pdf_path), 'name' => $paySlip->employee->employee_code.'_'.preg_replace('/[^a-zA-Z0-9]/', '_', $paySlip->period_label).'.pdf'], 200);
    }

    /**
     * Update pay slip status (mark as paid).
     */
    public function updateStatus(Request $request, PaySlip $paySlip): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Pending,Paid',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $oldStatus = $paySlip->status;
            $paySlip->update([
                'status' => $request->status,
                'paid_at' => $request->status === 'Paid' ? now() : null,
            ]);

            if($request->status === 'Paid'){
                $paySlip->update([
                    'pdf_path' => null
                ]);
            }

            UserLog::create([
                'user_id' => auth()->id(),
                'type' => 'pay_slip_status_updated',
                'metadata' => [
                    'pay_slip_id' => $paySlip->id,
                    'employee_id' => $paySlip->employee_id,
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                ],
            ]);

            return response()->json($paySlip);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get employees for pay slip generation.
     */
    public function getEmployees(Request $request): JsonResponse
    {
        $query = Employee::with(['department', 'currentSalaryStructure.structure']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        $employees = $query->where('status', 'active')->get();

        return response()->json($employees);
    }

    /**
     * Regenerate an existing pay slip with current salary structure.
     */
    public function regenerate(PaySlip $paySlip): JsonResponse
    {
        try {
            // Check if pay slip is paid and prevent regeneration unless explicitly allowed
            if ($paySlip->isPaid()) {
                return response()->json([
                    'error' => 'Cannot regenerate paid pay slip. Please contact system administrator if this is required.'
                ], 403);
            }

            // Load employee with current salary structure
            $employee = Employee::with('currentSalaryStructure.structure')->find($paySlip->employee_id);
            if (!$employee) {
                return response()->json(['error' => 'Employee not found'], 404);
            }

            // Check if employee has current salary structure
            $currentSalaryStructure = $employee->currentSalaryStructure?->structure;
            if (!$currentSalaryStructure) {
                return response()->json([
                    'error' => 'Employee does not have an assigned salary structure'
                ], 400);
            }
            

            // Store previous calculation data for history
            $previousCalculation = [
                'basic' => $paySlip->basic,
                'allowances' => $paySlip->allowances,
                'deductions' => $paySlip->deductions,
                'gross_salary' => $paySlip->gross_salary,
                'net_salary' => $paySlip->net_salary,
                'salary_structure_id' => $paySlip->salary_structure_id,
                'regenerated_at' => now()->toISOString(),
            ];

            // Generate new pay slip data using current salary structure
            $newPaySlipData = $this->paySlipGenerator->generatePaySlipData(
                $employee, 
                $currentSalaryStructure, 
                $paySlip->meta['overrides'] ?? []
            );

            // Update the pay slip with new calculation
            $paySlip->update([
                'salary_structure_id' => $currentSalaryStructure->id,
                'basic' => $newPaySlipData['basic'],
                'allowances' => $newPaySlipData['allowances'],
                'deductions' => $newPaySlipData['deductions'],
                'gross_salary' => $newPaySlipData['gross_salary'],
                'net_salary' => $newPaySlipData['net_salary'],
                'generated_at' => now(),
                'generated_by' => auth()->id(),
                'meta' => array_merge($paySlip->meta ?? [], [
                    'previous_calculation' => $previousCalculation,
                    'regenerated_at' => now()->toISOString(),
                    'regenerated_by' => auth()->id(),
                ]),
            ]);

            // Regenerate PDF if it exists
            $pdfGenerator = new PdfGenerator();
            $pdfPath = $pdfGenerator->regeneratePaySlipPdf($paySlip);

            // Log the regeneration action
            UserLog::create([
                'user_id' => auth()->id(),
                'type' => 'payslip_regenerate',
                'metadata' => [
                    'pay_slip_id' => $paySlip->id,
                    'employee_id' => $paySlip->employee_id,
                    'previous_calculation' => $previousCalculation,
                    'new_calculation' => $newPaySlipData,
                    'pdf_regenerated' => $pdfPath ? true : false,
                    'regenerated_at' => now()->toISOString(),
                ],
            ]);

            return response()->json([
                'message' => 'Pay slip regenerated successfully',
                'pay_slip' => $paySlip->load(['employee.department', 'salaryStructure', 'generator']),
                'pdf_regenerated' => $pdfPath ? true : false,
                'changes' => [
                    'basic' => [
                        'old' => $previousCalculation['basic'],
                        'new' => $newPaySlipData['basic'],
                    ],
                    'gross_salary' => [
                        'old' => $previousCalculation['gross_salary'],
                        'new' => $newPaySlipData['gross_salary'],
                    ],
                    'net_salary' => [
                        'old' => $previousCalculation['net_salary'],
                        'new' => $newPaySlipData['net_salary'],
                    ],
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Prepare period data from request.
     */
    private function preparePeriodData(Request $request): array
    {
        if ($request->period_type === 'month') {
            return [
                'month' => $request->period_month,
                'year' => $request->period_year,
            ];
        } else {
            return [
                'week_start' => $request->period_week_start,
                'week_end' => $request->period_week_end,
            ];
        }
    }
}