<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Leave Application Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
        }
        .field {
            margin-bottom: 15px;
        }
        .field-label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }
        .field-value {
            color: #6c757d;
        }
        .dates {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>New Leave Application</h2>
        <p>An employee has submitted a new leave application that requires your review.</p>
    </div>

    <div class="content">
        <div class="field">
            <div class="field-label">Employee Name:</div>
            <div class="field-value">{{ $leaveRequest->employee->name }}</div>
        </div>

        <div class="field">
            <div class="field-label">Employee ID:</div>
            <div class="field-value">{{ $leaveRequest->employee->employee_code }}</div>
        </div>

        <div class="field">
            <div class="field-label">Department:</div>
            <div class="field-value">{{ $leaveRequest->employee->department->name ?? 'N/A' }}</div>
        </div>

        <div class="field">
            <div class="field-label">Subject:</div>
            <div class="field-value">{{ $leaveRequest->subject }}</div>
        </div>

        <div class="field">
            <div class="field-label">Leave Type:</div>
            <div class="field-value">{{ ucfirst($leaveRequest->leave_type) }} Leave</div>
        </div>

        <div class="field">
            <div class="field-label">Leave Dates:</div>
            <div class="field-value">
                <div class="dates">
                    {{ $formattedDates }}
                </div>
            </div>
        </div>

        <div class="field">
            <div class="field-label">Application Details:</div>
            <div class="field-value">
                <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; white-space: pre-wrap;">{{ $leaveRequest->application_body }}</div>
            </div>
        </div>

        <div class="field">
            <div class="field-label">Submitted On:</div>
            <div class="field-value">{{ $leaveRequest->created_at->format('F d, Y \a\t g:i A') }}</div>
        </div>
    </div>

    <div class="footer">
        <p>This is an automated notification from the HR Management System.</p>
        <p>Please log in to the system to review and take action on this leave application.</p>
    </div>
</body>
</html>
