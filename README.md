# HRM System (Laravel 10 + Vue 3 + Sanctum)

A comprehensive Human Resource Management system built with Laravel 10, Vue 3, and Laravel Sanctum. Features role-based access control, employee management, attendance tracking, leave management, and admin management.

## 🚀 Features

### Core Modules
- **Authentication System** - Secure login/logout with password reset
- **Employee Management** - Complete CRUD for employees with departments and designations
- **Attendance Management** - Daily attendance tracking with check-in/out times
- **Leave Management** - Leave request system with approval workflow
- **Holiday Management** - Company holiday calendar
- **Admin Management** - User administration (System Admin only)

### Role-Based Access Control
- **System Admin** - Full access to all modules and features
- **HR Manager** - Access to Employee Management, Attendance, Payroll, Reports
- **Supervisor** - Read-only access to most modules (no create/edit/delete)

### Technical Features
- **SPA Architecture** - Single Page Application with Vue 3
- **API-First Design** - RESTful APIs with Laravel Sanctum authentication
- **Responsive UI** - Tailwind CSS with smooth animations and transitions
- **Soft Deletes** - Data preservation with soft deletion
- **Activity Logging** - Comprehensive audit trail for all actions
- **File Uploads** - Avatar uploads with Laravel storage

## 🛠 Tech Stack

### Backend
- **Laravel 10** - PHP framework
- **Laravel Sanctum** - API authentication
- **MySQL** - Database
- **Eloquent ORM** - Database management

### Frontend
- **Vue 3** - JavaScript framework
- **Vue Router** - Client-side routing
- **Axios** - HTTP client
- **Tailwind CSS** - Utility-first CSS framework

## 📋 Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js 16+ and npm
- MySQL 5.7+ or MariaDB 10.3+

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/rdhafiz/HRMS
   cd HRMS
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database configuration**
   - Update `.env` with your database credentials
   - Run migrations and seeders
   ```bash
   php artisan migrate --seed
   ```

5. **Install Node.js dependencies**
   ```bash
   npm install
   ```

6. **Build assets**
   ```bash
   npm run dev
   # or for production
   npm run build
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

8. **Create storage link (for file uploads)**
   ```bash
   php artisan storage:link
   ```

## 🔐 Default Login Credentials

The system comes with a seeded admin user:
- **Email:** admin@example.com
- **Password:** password123
- **Role:** System Admin

## 📚 API Documentation

### Authentication Endpoints
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `GET /api/auth/user` - Get current user
- `POST /api/auth/forgot` - Request password reset
- `POST /api/auth/verify-reset-code` - Verify reset code
- `POST /api/auth/reset` - Reset password

### Profile Management (All authenticated admins)
- `GET /api/auth/profile` - Get current user's profile and last login timestamp
- `GET /api/auth/profile/logs?per_page=50&page=1` - Paginated activity logs (Action, Timestamp, IP)
- `POST /api/auth/profile` - Update profile
- `POST /api/auth/profile/change-password` - Change password

### Employee Management
- `GET /api/employment/employees` - List employees
- `POST /api/employment/employees` - Create employee
- `GET /api/employment/employees/{id}` - Get employee
- `PUT /api/employment/employees/{id}` - Update employee
- `DELETE /api/employment/employees/{id}` - Delete employee

### Department Management
- `GET /api/employment/departments` - List departments
- `POST /api/employment/departments` - Create department
- `GET /api/employment/departments/{id}` - Get department
- `PUT /api/employment/departments/{id}` - Update department
- `DELETE /api/employment/departments/{id}` - Delete department

### Designation Management
- `GET /api/employment/designations` - List designations
- `POST /api/employment/designations` - Create designation
- `GET /api/employment/designations/{id}` - Get designation
- `PUT /api/employment/designations/{id}` - Update designation
- `DELETE /api/employment/designations/{id}` - Delete designation

### Attendance Management
- `GET /api/employment/attendance` - List attendance records
- `POST /api/employment/attendance` - Create attendance record
- `PUT /api/employment/attendance/{id}` - Update attendance record
- `DELETE /api/employment/attendance/{id}` - Delete attendance record

### Leave Management
- `GET /api/employment/leave-requests` - List leave requests
- `POST /api/employment/leave-requests` - Create leave request
- `PUT /api/employment/leave-requests/{id}` - Update leave request
- `POST /api/employment/leave-requests/{id}/decision` - Approve/reject leave
- `DELETE /api/employment/leave-requests/{id}` - Delete leave request

### Holiday Management
- `GET /api/employment/holidays` - List holidays
- `POST /api/employment/holidays` - Create holiday
- `PUT /api/employment/holidays/{id}` - Update holiday
- `DELETE /api/employment/holidays/{id}` - Delete holiday

### Admin Management (System Admin only)
- `GET /api/admins` - List admins
- `POST /api/admins` - Create admin
- `GET /api/admins/{id}` - Get admin
- `POST /api/admins/{id}` - Update admin
- `DELETE /api/admins/{id}` - Delete admin

## 🎨 Frontend Routes

### Public Routes
- `/` - Login page
- `/forgot` - Forgot password
- `/reset` - Reset password

### Protected Routes (Requires Authentication)
- `/dashboard` - Dashboard
- `/departments` - Department list
- `/departments/create` - Create department
- `/departments/:id/edit` - Edit department
- `/designations` - Designation list
- `/designations/create` - Create designation
- `/designations/:id/edit` - Edit designation
- `/employees` - Employee list
- `/employees/create` - Create employee
- `/employees/:id/edit` - Edit employee
- `/employees/:id` - View employee
- `/attendance/daily` - Daily attendance
- `/attendance/leaves` - Leave requests
- `/attendance/leaves/create` - Create leave request
- `/attendance/leaves/:id/edit` - Edit leave request
- `/attendance/holidays` - Holiday list
- `/attendance/holidays/create` - Create holiday
- `/attendance/holidays/:id/edit` - Edit holiday
- `/admins` - Admin list (System Admin only)
- `/admins/create` - Create admin (System Admin only)
- `/admins/:id/edit` - Edit admin (System Admin only)
  
#### Profile
- `/profile` - Admin Profile (view own profile, last login, activity logs)
- `/profile/update` - Update Profile (name, avatar)
- `/profile/change-password` - Change own password

## 🔒 Role-Based Access Control

### System Admin (admin_type: 1)
- Full access to all modules
- Can create, edit, delete all records
- Access to Admin Management module
- Can manage other users

### HR Manager (admin_type: 2)
- Access to Employee Management
- Access to Attendance Management
- Access to Leave Management
- Access to Holiday Management
- Can create, edit, delete records in allowed modules
- Cannot access Admin Management

### Supervisor (admin_type: 3)
- Read-only access to most modules
- Can view employee information
- Can view attendance records
- Can view leave requests and holidays
- Cannot create, edit, or delete any records
- Cannot access Admin Management

## 📁 Project Structure

```
hrm.ridwan/
├── app/
│   ├── Constants/
│   │   ├── UserRoles.php
│   │   ├── AttendanceStatus.php
│   │   └── LeaveTypes.php
│   ├── Http/Controllers/Api/
│   │   ├── AdminController.php
│   │   ├── AttendanceController.php
│   │   ├── DepartmentController.php
│   │   ├── DesignationController.php
│   │   ├── EmployeeController.php
│   │   ├── HolidayController.php
│   │   └── LeaveRequestController.php
│   └── Models/
│       ├── Attendance.php
│       ├── Department.php
│       ├── Designation.php
│       ├── Employee.php
│       ├── Holiday.php
│       ├── LeaveRequest.php
│       ├── User.php
│       └── UserLog.php
├── database/migrations/
│   ├── 2014_10_12_000000_create_users_table.php
│   ├── 2025_09_08_020000_create_departments_table.php
│   ├── 2025_09_08_020100_create_designations_table.php
│   ├── 2025_09_08_030000_create_employees_table.php
│   ├── 2025_09_08_040000_create_attendances_table.php
│   ├── 2025_09_08_040100_create_leave_requests_table.php
│   ├── 2025_09_08_040200_create_holidays_table.php
│   └── 2025_09_08_050000_add_soft_deletes_to_users_table.php
├── resources/js/
│   ├── components/
│   │   ├── Header.vue
│   │   ├── NavBar.vue
│   │   └── SideNav.vue
│   ├── layouts/
│   │   ├── AuthLayout.vue
│   │   └── DashboardLayout.vue
│   ├── pages/
│   │   ├── Admins/
│   │   │   ├── Admins.vue
│   │   │   └── AdminForm.vue
│   │   ├── Profile/
│   │   │   ├── AdminProfile.vue
│   │   │   ├── UpdateProfile.vue
│   │   │   └── ChangePassword.vue
│   │   ├── Attendance/
│   │   │   ├── DailyAttendance.vue
│   │   │   ├── LeaveRequests.vue
│   │   │   ├── LeaveRequestForm.vue
│   │   │   ├── Holidays.vue
│   │   │   └── HolidayForm.vue
│   │   ├── Auth/
│   │   │   ├── Login.vue
│   │   │   ├── ForgotPassword.vue
│   │   │   └── ResetPassword.vue
│   │   ├── Dashboard/
│   │   │   └── Dashboard.vue
│   │   └── Employment/
│   │       ├── Departments/
│   │       │   ├── Departments.vue
│   │       │   └── DepartmentForm.vue
│   │       ├── Designations/
│   │       │   ├── Designations.vue
│   │       │   └── DesignationForm.vue
│   │       └── Employees/
│   │           ├── Employees.vue
│   │           ├── EmployeeForm.vue
│   │           └── EmployeeView.vue
│   └── router/
│       └── index.js
└── routes/
    ├── api.php
    └── web.php
```

## 🎯 Key Features

### User Experience
- **Responsive Design** - Works on desktop, tablet, and mobile
- **Smooth Animations** - Tailwind CSS transitions and Vue animations
- **Intuitive Navigation** - Role-based sidebar with active state highlighting
- **Real-time Feedback** - Form validation and success/error messages

### Security
- **CSRF Protection** - Laravel's built-in CSRF protection
- **XSS Protection** - Input sanitization and output escaping
- **SQL Injection Prevention** - Eloquent ORM with parameterized queries
- **Role-based Access** - Frontend and backend permission checks

### Performance
- **Lazy Loading** - Components loaded on demand
- **Efficient Queries** - Eager loading to prevent N+1 queries
- **Caching** - User data caching in frontend
- **Optimized Assets** - Vite for fast development and optimized builds

## 🔧 Configuration

### Environment Variables
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hrm_system
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=cookie
SESSION_LIFETIME=120
SESSION_DOMAIN=localhost
SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:5173

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

### File Storage
- Avatar uploads are stored in `storage/app/public/avatars`
- Run `php artisan storage:link` to create symbolic link

### Validation Rules (Profile)
- Name: required, string, max 255
- Avatar: image, formats: jpg/jpeg/png/webp, max 2MB
- Change password: current password must match; new password min 8 and must include letters, numbers, and symbols; `new_password_confirmation` must match; optional `logout=true` to force re-login

## 🧪 Testing

```bash
# Run PHP tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
```

## 📝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🤝 Support

For support, email support@example.com or create an issue in the repository.

---

**Built with ❤️ using Laravel 10, Vue 3, and Tailwind CSS**
