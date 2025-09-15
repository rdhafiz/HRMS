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
        if (!$paySlip->pdf_path || !Storage::exists($paySlip->pdf_path)) {
            $pdfGenerator->generatePaySlipPdf($paySlip);
            $paySlip->refresh();
        }

        if (!$paySlip->pdf_path || !Storage::exists($paySlip->pdf_path)) {
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

        return Storage::download($paySlip->pdf_path);
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