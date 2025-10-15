<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmailNotification;
use App\Models\Employee;
use App\Models\Department;
use App\Mail\EmailNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class EmailNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EmailNotification::with('sender');

        // Search by subject
        if ($request->has('search') && $request->search) {
            $query->where('subject', 'like', '%' . $request->search . '%');
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('sent_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('sent_at', '<=', $request->date_to);
        }

        // Filter by department
        if ($request->has('department_id') && $request->department_id) {
            $query->whereJsonContains('recipients', ['department_id' => (int)$request->department_id]);
        }

        // Filter by employee
        if ($request->has('employee_id') && $request->employee_id) {
            $query->whereJsonContains('recipients', ['employee_id' => (int)$request->employee_id]);
        }

        $notifications = $query->orderBy('sent_at', 'desc')->paginate(15);

        return response()->json($notifications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'recipient_type' => 'required|in:all,department,specific',
            'department_id' => 'required_if:recipient_type,department|exists:departments,id',
            'employee_ids' => 'required_if:recipient_type,specific|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png,gif|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get recipients based on type
        $recipients = $this->getRecipients($request);

        if (empty($recipients)) {
            return response()->json(['message' => 'No recipients found'], 400);
        }

        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                // Validate file
                if ($file->isValid()) {
                    $path = $file->store('email-attachments', 'public');
                    
                    // Verify file was stored successfully
                    if (Storage::disk('public')->exists($path)) {
                        $attachments[] = [
                            'original_name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'mime_type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                        ];
                        
                        \Log::info('File stored successfully: ' . $path);
                    } else {
                        \Log::error('Failed to store file: ' . $file->getClientOriginalName());
                    }
                } else {
                    \Log::error('Invalid file upload: ' . $file->getClientOriginalName());
                }
            }
        }

        // Create email notification record
        $emailNotification = EmailNotification::create([
            'subject' => $request->subject,
            'body' => $request->body ?: '<p>No content</p>', // Fallback for null body
            'recipients' => $recipients,
            'attachments' => $attachments,
            'sender_id' => Auth::id(),
            'sent_at' => now(),
        ]);

        // Send emails
        foreach ($recipients as $recipient) {
            Mail::to($recipient['email'])->send(new EmailNotificationMail($emailNotification));
        }

        return response()->json([
            'message' => 'Email notification sent successfully',
            'data' => $emailNotification->load('sender')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $notification = EmailNotification::with('sender')->findOrFail($id);
        return response()->json($notification);
    }

    /**
     * Get recipients based on request type
     */
    private function getRecipients(Request $request)
    {
        $recipients = [];

        switch ($request->recipient_type) {
            case 'all':
                $employees = Employee::with('department')->get();
                foreach ($employees as $employee) {
                    $recipients[] = [
                        'employee_id' => $employee->id,
                        'name' => $employee->name,
                        'email' => $employee->email,
                        'department_id' => $employee->department_id,
                        'department_name' => $employee->department->name ?? 'N/A',
                    ];
                }
                break;

            case 'department':
                $employees = Employee::with('department')
                    ->where('department_id', $request->department_id)
                    ->get();
                foreach ($employees as $employee) {
                    $recipients[] = [
                        'employee_id' => $employee->id,
                        'name' => $employee->name,
                        'email' => $employee->email,
                        'department_id' => $employee->department_id,
                        'department_name' => $employee->department->name ?? 'N/A',
                    ];
                }
                break;

            case 'specific':
                $employees = Employee::with('department')
                    ->whereIn('id', $request->employee_ids)
                    ->get();
                foreach ($employees as $employee) {
                    $recipients[] = [
                        'employee_id' => $employee->id,
                        'name' => $employee->name,
                        'email' => $employee->email,
                        'department_id' => $employee->department_id,
                        'department_name' => $employee->department->name ?? 'N/A',
                    ];
                }
                break;
        }

        return $recipients;
    }

    /**
     * Get employees for dropdown
     */
    public function getEmployees(Request $request)
    {
        $query = Employee::with('department');

        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        $employees = $query->get(['id', 'first_name', 'last_name', 'email', 'department_id'])
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'department_id' => $employee->department_id,
                    'department_name' => $employee->department->name ?? 'N/A',
                ];
            });

        return response()->json($employees);
    }

    /**
     * Get departments for dropdown
     */
    public function getDepartments()
    {
        $departments = Department::all(['id', 'name']);
        return response()->json($departments);
    }
}
