<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\MicrosoftAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\DesignationController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\HolidayController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\SalaryStructureController;
use App\Http\Controllers\Api\EmployeeSalaryStructureController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\BrandingController;
use App\Http\Controllers\Api\PaySlipController;
use App\Http\Controllers\Api\EmployeeProfileController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\TrainingAndPolicyController;
use App\Http\Controllers\Api\EmailNotificationController;
use App\Http\Controllers\Api\TrainingPolicyCategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Microsoft OAuth API routes (for SPA)
Route::middleware(['web', 'secure.microsoft'])->prefix('auth')->group(function () {
    Route::get('/microsoft', [MicrosoftAuthController::class, 'redirectToMicrosoft'])->name('api.login.microsoft');
    Route::get('/microsoft/callback', [MicrosoftAuthController::class, 'handleMicrosoftCallback'])->name('api.login.microsoft.callback');
});

// Authentication endpoints
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('/forgot', [PasswordResetController::class, 'sendResetCode'])->middleware('throttle:password-reset');
    Route::post('/verify-reset-code', [PasswordResetController::class, 'verifyCode'])->middleware('throttle:password-reset');
    Route::post('/reset', [PasswordResetController::class, 'reset'])->middleware('throttle:password-reset');

    Route::middleware(['auth:sanctum', 'throttle.custom:api'])->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);

        // Profile
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::get('/profile/logs', [ProfileController::class, 'logs']);
        Route::post('/profile', [ProfileController::class, 'update']);
        Route::post('/profile/change-password', [ProfileController::class, 'changePassword']);
    });
});

// Admins Management (System Admin only)
Route::middleware(['auth:sanctum', 'role:system_admin'])->prefix('admins')->group(function () {
    Route::get('/', [AdminController::class, 'index']);
    Route::post('/', [AdminController::class, 'store']);
    Route::get('/{admin}', [AdminController::class, 'show']);
    Route::post('/{admin}', [AdminController::class, 'update']);
    Route::delete('/{admin}', [AdminController::class, 'destroy']);
});

// Branding (System Admin only)
Route::middleware(['auth:sanctum', 'role:system_admin'])->group(function () {
    Route::get('/branding', [BrandingController::class, 'show']);
    Route::post('/branding', [BrandingController::class, 'update']);
});

// Protected dashboard data with role-based access example
Route::middleware(['auth:sanctum', 'role:system_admin|hr_manager'])
    ->get('/dashboard/data', [DashboardController::class, 'index']);

// Dashboard API endpoints for individual cards
Route::middleware(['auth:sanctum', 'role:system_admin|hr_manager', 'throttle:dashboard'])->prefix('dashboard')->group(function () {
    Route::get('/employees', [App\Http\Controllers\Api\DashboardApiController::class, 'employees']);
    Route::get('/attendance', [App\Http\Controllers\Api\DashboardApiController::class, 'attendance']);
    Route::get('/leaves', [App\Http\Controllers\Api\DashboardApiController::class, 'leaves']);
    Route::get('/holidays', [App\Http\Controllers\Api\DashboardApiController::class, 'holidays']);
    Route::get('/activity', [App\Http\Controllers\Api\DashboardApiController::class, 'activity']);
    Route::get('/departments', [App\Http\Controllers\Api\DashboardApiController::class, 'departments']);
    Route::get('/activity-logs', [App\Http\Controllers\Api\DashboardApiController::class, 'activityLogs']);
});

// Employment Management
Route::middleware(['auth:sanctum', 'throttle.custom:api'])->prefix('employment')->group(function () {
    // Departments
    Route::get('departments', [DepartmentController::class, 'index']);
    Route::get('departments/{department}', [DepartmentController::class, 'show']);
    Route::middleware('role:system_admin|hr_manager')->group(function () {
        Route::post('departments', [DepartmentController::class, 'store']);
        Route::put('departments/{department}', [DepartmentController::class, 'update']);
        Route::delete('departments/{department}', [DepartmentController::class, 'destroy']);
    });

    // Designations
    Route::get('designations', [DesignationController::class, 'index']);
    Route::get('designations/{designation}', [DesignationController::class, 'show']);
    Route::middleware('role:system_admin|hr_manager')->group(function () {
        Route::post('designations', [DesignationController::class, 'store']);
        Route::put('designations/{designation}', [DesignationController::class, 'update']);
        Route::delete('designations/{designation}', [DesignationController::class, 'destroy']);
    });

    // Employees
    Route::get('employees', [EmployeeController::class, 'index']);
    Route::get('employees/{employee}', [EmployeeController::class, 'show']);
    Route::middleware('role:system_admin|hr_manager')->group(function () {
        Route::post('employees', [EmployeeController::class, 'store']);
        Route::put('employees/{employee}', [EmployeeController::class, 'update']);
        Route::delete('employees/{employee}', [EmployeeController::class, 'destroy']);
    });

    // Attendance Module
    // Attendance
    Route::get('attendance', [AttendanceController::class, 'index']);
    Route::middleware('role:system_admin|hr_manager')->group(function () {
        Route::post('attendance', [AttendanceController::class, 'store']);
        Route::put('attendance/{attendance}', [AttendanceController::class, 'update']);
        Route::delete('attendance/{attendance}', [AttendanceController::class, 'destroy']);
    });

    // Leave Requests
    Route::get('leave-requests', [LeaveRequestController::class, 'index']);
    Route::middleware('role:system_admin|hr_manager')->group(function () {
        Route::post('leave-requests', [LeaveRequestController::class, 'store']);
        Route::put('leave-requests/{leaveRequest}', [LeaveRequestController::class, 'update']);
        Route::post('leave-requests/{leaveRequest}/decision', [LeaveRequestController::class, 'approve']);
        Route::delete('leave-requests/{leaveRequest}', [LeaveRequestController::class, 'destroy']);
    });

    // Holidays
    Route::get('holidays', [HolidayController::class, 'index']);
    Route::middleware('role:system_admin|hr_manager')->group(function () {
        Route::post('holidays', [HolidayController::class, 'store']);
        Route::put('holidays/{holiday}', [HolidayController::class, 'update']);
        Route::delete('holidays/{holiday}', [HolidayController::class, 'destroy']);
    });

    // Payroll - Salary Structures
    Route::get('salary-structures', [SalaryStructureController::class, 'index']);
    Route::get('salary-structures/{salaryStructure}', [SalaryStructureController::class, 'show']);
    Route::middleware('role:system_admin|hr_manager')->group(function () {
        Route::post('salary-structures', [SalaryStructureController::class, 'store']);
        Route::put('salary-structures/{salaryStructure}', [SalaryStructureController::class, 'update']);
        Route::delete('salary-structures/{salaryStructure}', [SalaryStructureController::class, 'destroy']);
        Route::post('employee-salary-structures', [EmployeeSalaryStructureController::class, 'store']);
    });
    Route::get('employee-salary-structures/{employee}', [EmployeeSalaryStructureController::class, 'showByEmployee']);
    Route::get('employee-salary-structures/{employee}/history', [EmployeeSalaryStructureController::class, 'history']);

    // Payroll - Pay Slips
    Route::get('pay-slips', [PaySlipController::class, 'index']);
    Route::get('pay-slips/employees', [PaySlipController::class, 'getEmployees']);
    Route::get('pay-slips/{paySlip}/download', [PaySlipController::class, 'download']);
    Route::middleware('role:system_admin|hr_manager')->group(function () {
        Route::post('pay-slips', [PaySlipController::class, 'store']);
        Route::post('pay-slips/generate-batch', [PaySlipController::class, 'generateBatch']);
        Route::put('pay-slips/{paySlip}/status', [PaySlipController::class, 'updateStatus']);
        Route::post('pay-slips/{paySlip}/regenerate', [PaySlipController::class, 'regenerate']);
    });
    Route::get('pay-slips/{paySlip}', [PaySlipController::class, 'show']);

    // Training & Policies
    Route::get('trainings-policies', [TrainingAndPolicyController::class, 'index']);
    Route::get('trainings-policies/{trainingAndPolicy}', [TrainingAndPolicyController::class, 'show']);
    Route::middleware('role:system_admin|hr_manager')->group(function () {
        Route::post('trainings-policies', [TrainingAndPolicyController::class, 'store']);
        Route::put('trainings-policies/{trainingAndPolicy}', [TrainingAndPolicyController::class, 'update']);
        Route::delete('trainings-policies/{trainingAndPolicy}', [TrainingAndPolicyController::class, 'destroy']);
    });

    // Training & Policy Categories
    Route::get('training-policy-categories', [TrainingPolicyCategoryController::class, 'index']);
    Route::get('training-policy-categories/parents', [TrainingPolicyCategoryController::class, 'getParents']);
    Route::get('training-policy-categories/{trainingPolicyCategory}', [TrainingPolicyCategoryController::class, 'show']);
    Route::middleware('role:system_admin|hr_manager')->group(function () {
        Route::post('training-policy-categories', [TrainingPolicyCategoryController::class, 'store']);
        Route::put('training-policy-categories/{trainingPolicyCategory}', [TrainingPolicyCategoryController::class, 'update']);
        Route::delete('training-policy-categories/{trainingPolicyCategory}', [TrainingPolicyCategoryController::class, 'destroy']);
    });
});

// Employee Profile (Employee role only)
Route::middleware(['auth:sanctum', 'role:employee', 'throttle.custom:api'])->prefix('employee')->group(function () {
    Route::get('/profile', [EmployeeProfileController::class, 'index']);
    Route::get('/activity-logs', [EmployeeProfileController::class, 'activityLogs']);
    Route::post('/profile', [EmployeeProfileController::class, 'updateProfile']);
    Route::post('/change-password', [EmployeeProfileController::class, 'changePassword']);
    
    // Training & Policies
    Route::get('/training-policies', [App\Http\Controllers\Api\EmployeeTrainingPolicyController::class, 'index']);
    Route::post('/training-policies/save', [App\Http\Controllers\Api\EmployeeTrainingPolicyController::class, 'save']);
});

// Employee Holidays (Employee role only)
Route::middleware(['auth:sanctum', 'role:employee', 'throttle.custom:api'])->group(function () {
    Route::get('/holidays/employee', [HolidayController::class, 'employeeHolidays']);
});

// Employee Attendance (Employee role only)
Route::middleware(['auth:sanctum', 'role:employee', 'throttle.custom:api'])->group(function () {
    Route::get('/attendances/employee', [AttendanceController::class, 'employeeAttendance']);
});

// Employee Leave Management (Employee role only)
Route::middleware(['auth:sanctum', 'role:employee', 'throttle.custom:api'])->prefix('leaves')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\LeaveController::class, 'index']);
    Route::post('/', [App\Http\Controllers\Api\LeaveController::class, 'store']);
});

// Email Notifications
Route::middleware(['auth:sanctum', 'throttle.custom:api'])->prefix('email-notifications')->group(function () {
    Route::get('/', [EmailNotificationController::class, 'index']);
    Route::get('/departments', [EmailNotificationController::class, 'getDepartments']);
    Route::get('/employees', [EmailNotificationController::class, 'getEmployees']);
    Route::get('/{id}', [EmailNotificationController::class, 'show']);

    Route::middleware('role:system_admin|hr_manager')->group(function () {
        Route::post('/', [EmailNotificationController::class, 'store']);
    });
});
