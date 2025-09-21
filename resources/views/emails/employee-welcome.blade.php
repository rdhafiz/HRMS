<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to HR Management System</title>
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
            text-align: center;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
        }
        .credentials {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .footer {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            font-size: 12px;
            color: #6c757d;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to HR Management System</h1>
        <p>Your account has been created successfully</p>
    </div>

    <div class="content">
        <p>Dear {{ $employee->name }},</p>
        
        <p>Welcome to our HR Management System! Your employee account has been created and you can now access the system with the following credentials:</p>
        
        <div class="credentials">
            <h3>Login Credentials:</h3>
            <p><strong>Email:</strong> {{ $employee->email }}</p>
            <p><strong>Password:</strong> {{ $password }}</p>
        </div>
        
        <p><strong>Important:</strong> Please change your password after your first login for security purposes.</p>
        
        <p>With your employee account, you can access:</p>
        <ul>
            <li>Your personal dashboard</li>
            <li>View and update your profile</li>
            <li>View your leave balance and holidays</li>
            <li>Create leave applications</li>
            <li>View your attendance records</li>
        </ul>
        
        <p>To get started, please log in to the system using the credentials provided above.</p>
        
        <a href="{{ url('/') }}" class="button">Login to System</a>
    </div>

    <div class="footer">
        <p>This email was sent from the HR Management System.</p>
        <p>If you have any questions, please contact your HR department.</p>
        <p><strong>Note:</strong> Please keep your login credentials secure and do not share them with anyone.</p>
    </div>
</body>
</html>

