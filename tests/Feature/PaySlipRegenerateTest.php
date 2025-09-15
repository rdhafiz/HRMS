<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use App\Models\SalaryStructure;
use App\Models\EmployeeSalaryStructure;
use App\Models\PaySlip;
use App\Models\SalaryComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class PaySlipRegenerateTest extends TestCase
{
    use RefreshDatabase;

    protected Employee $employee;
    protected PaySlip $paySlip;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->createTestData();
    }

    private function createTestData()
    {
        // Create department
        $department = Department::create([
            'name' => 'Test Department',
            'description' => 'Test Department Description',
        ]);

        // Create designation
        $designation = Designation::create([
            'name' => 'Test Designation',
            'description' => 'Test Designation Description',
        ]);

        // Create salary structure
        $salaryStructure = SalaryStructure::create([
            'name' => 'Test Salary Structure',
            'basic_salary' => 50000.00,
            'description' => 'Test Salary Structure Description',
        ]);

        // Create salary components
        SalaryComponent::create([
            'salary_structure_id' => $salaryStructure->id,
            'name' => 'Housing Allowance',
            'type' => 'allowance',
            'amount' => 10000.00,
        ]);

        SalaryComponent::create([
            'salary_structure_id' => $salaryStructure->id,
            'name' => 'Transport Allowance',
            'type' => 'allowance',
            'amount' => 5000.00,
        ]);

        SalaryComponent::create([
            'salary_structure_id' => $salaryStructure->id,
            'name' => 'Tax Deduction',
            'type' => 'deduction',
            'amount' => 8000.00,
        ]);

        // Create employee
        $this->employee = Employee::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@test.com',
            'employee_code' => 'EMP001',
            'department_id' => $department->id,
            'designation_id' => $designation->id,
            'status' => 'active',
        ]);

        // Assign salary structure to employee
        EmployeeSalaryStructure::create([
            'employee_id' => $this->employee->id,
            'salary_structure_id' => $salaryStructure->id,
            'effective_from' => now()->subMonth(),
            'effective_to' => null,
        ]);

        // Create pay slip
        $this->paySlip = PaySlip::create([
            'employee_id' => $this->employee->id,
            'salary_structure_id' => $salaryStructure->id,
            'period_type' => 'month',
            'period_month' => 1,
            'period_year' => 2024,
            'basic' => 50000.00,
            'allowances' => ['Housing Allowance' => 10000.00, 'Transport Allowance' => 5000.00],
            'deductions' => ['Tax Deduction' => 8000.00],
            'gross_salary' => 65000.00,
            'net_salary' => 57000.00,
            'status' => 'Pending',
            'generated_at' => now(),
            'generated_by' => 1,
        ]);

        // Create admin user
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'system_admin',
        ]);
    }

    public function test_regenerate_pay_slip_success()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/employment/pay-slips/{$this->paySlip->id}/regenerate");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'pay_slip' => [
                    'id',
                    'employee_id',
                    'basic',
                    'allowances',
                    'deductions',
                    'gross_salary',
                    'net_salary',
                ],
                'pdf_regenerated',
                'changes' => [
                    'basic' => ['old', 'new'],
                    'gross_salary' => ['old', 'new'],
                    'net_salary' => ['old', 'new'],
                ],
            ]);

        // Verify the pay slip was updated
        $this->paySlip->refresh();
        $this->assertEquals(50000.00, $this->paySlip->basic);
        $this->assertEquals(65000.00, $this->paySlip->gross_salary);
        $this->assertEquals(57000.00, $this->paySlip->net_salary);
    }

    public function test_regenerate_paid_pay_slip_fails()
    {
        // Mark pay slip as paid
        $this->paySlip->update(['status' => 'Paid', 'paid_at' => now()]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/employment/pay-slips/{$this->paySlip->id}/regenerate");

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'Cannot regenerate paid pay slip. Please contact system administrator if this is required.'
            ]);
    }

    public function test_regenerate_pay_slip_unauthorized()
    {
        // Create a regular user without admin privileges
        $regularUser = User::create([
            'name' => 'Regular User',
            'email' => 'regular@test.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
        ]);

        $response = $this->actingAs($regularUser, 'sanctum')
            ->getJson("/api/employment/pay-slips/{$this->paySlip->id}/regenerate");

        $response->assertStatus(403);
    }

    public function test_regenerate_pay_slip_not_found()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/employment/pay-slips/99999/regenerate');

        $response->assertStatus(404);
    }
}
