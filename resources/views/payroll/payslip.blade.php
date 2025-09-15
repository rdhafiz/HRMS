<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Slip - {{ $paySlip->employee->first_name }} {{ $paySlip->employee->last_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .payslip-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .company-logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .company-address {
            font-size: 12px;
            opacity: 0.9;
        }
        .payslip-title {
            background: #f8f9fa;
            padding: 20px 30px;
            border-bottom: 2px solid #e9ecef;
        }
        .payslip-title h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .payslip-title .period {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        .content {
            padding: 30px;
        }
        .employee-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        .employee-details h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .employee-details p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }
        .salary-breakdown {
            margin-bottom: 30px;
        }
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .breakdown-table th,
        .breakdown-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        .breakdown-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        .breakdown-table .amount {
            text-align: right;
            font-weight: 600;
        }
        .breakdown-table .total-row {
            background: #e9ecef;
            font-weight: bold;
            font-size: 16px;
        }
        .breakdown-table .net-row {
            background: #d4edda;
            color: #155724;
            font-size: 18px;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        .summary-item {
            text-align: center;
        }
        .summary-item .label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        .summary-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .footer {
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
            height: 40px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="payslip-container">
        <!-- Header -->
        <div class="header">
            <div class="company-logo">{{ $branding->company_name ?? 'HRM System' }}</div>
            <div class="company-name">{{ $branding->company_name ?? 'Your Company Name' }}</div>
            <div class="company-address">
                {{ $branding->address ?? 'Company Address' }}<br>
                {{ $branding->phone ?? 'Phone: +1 234 567 8900' }} | {{ $branding->email ?? 'Email: info@company.com' }}
            </div>
        </div>

        <!-- Pay Slip Title -->
        <div class="payslip-title">
            <h1>PAY SLIP</h1>
            <div class="period">{{ $paySlip->period_label }}</div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Employee Information -->
            <div class="employee-info">
                <div class="employee-details">
                    <h3>{{ $paySlip->employee->first_name }} {{ $paySlip->employee->last_name }}</h3>
                    <p><strong>Employee ID:</strong> {{ $paySlip->employee->employee_code }}</p>
                    <p><strong>Department:</strong> {{ $paySlip->employee->department->name ?? 'N/A' }}</p>
                    <p><strong>Designation:</strong> {{ $paySlip->employee->designation->name ?? 'N/A' }}</p>
                    <p><strong>Generated:</strong> {{ $paySlip->generated_at->format('M d, Y') }}</p>
                </div>
                <div class="employee-details">
                    <p><strong>Status:</strong> 
                        <span class="status-badge status-{{ strtolower($paySlip->status) }}">
                            {{ $paySlip->status }}
                        </span>
                    </p>
                    @if($paySlip->paid_at)
                        <p><strong>Paid On:</strong> {{ $paySlip->paid_at->format('M d, Y') }}</p>
                    @endif
                </div>
            </div>

            <!-- Salary Breakdown -->
            <div class="salary-breakdown">
                <h3>Salary Breakdown</h3>
                <table class="breakdown-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="amount">Amount (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Basic Salary -->
                        <tr>
                            <td>Basic Salary</td>
                            <td class="amount">{{ number_format($paySlip->basic, 2) }}</td>
                        </tr>

                        <!-- Allowances -->
                        @if($paySlip->allowances && count($paySlip->allowances) > 0)
                            @foreach($paySlip->allowances as $name => $amount)
                                <tr>
                                    <td>{{ $name }}</td>
                                    <td class="amount">{{ number_format($amount, 2) }}</td>
                                </tr>
                            @endforeach
                        @endif

                        <!-- Gross Salary -->
                        <tr class="total-row">
                            <td><strong>Gross Salary</strong></td>
                            <td class="amount"><strong>{{ number_format($paySlip->gross_salary, 2) }}</strong></td>
                        </tr>

                        <!-- Deductions -->
                        @if($paySlip->deductions && count($paySlip->deductions) > 0)
                            @foreach($paySlip->deductions as $name => $amount)
                                <tr>
                                    <td>{{ $name }}</td>
                                    <td class="amount">-{{ number_format($amount, 2) }}</td>
                                </tr>
                            @endforeach
                        @endif

                        <!-- Net Salary -->
                        <tr class="net-row">
                            <td><strong>Net Salary</strong></td>
                            <td class="amount"><strong>₹{{ number_format($paySlip->net_salary, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div class="summary">
                <div class="summary-item">
                    <div class="label">Basic Salary</div>
                    <div class="value">₹{{ number_format($paySlip->basic, 2) }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Total Allowances</div>
                    <div class="value">₹{{ number_format($paySlip->total_allowances, 2) }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Total Deductions</div>
                    <div class="value">₹{{ number_format($paySlip->total_deductions, 2) }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Net Salary</div>
                    <div class="value">₹{{ number_format($paySlip->net_salary, 2) }}</div>
                </div>
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>Employee Signature</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>HR Manager Signature</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is a computer generated pay slip and does not require a signature.</p>
            <p>Generated on {{ now()->format('M d, Y \a\t h:i A') }} | HRM System v1.0</p>
        </div>
    </div>
</body>
</html>
