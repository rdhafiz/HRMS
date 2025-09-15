# Payroll Module Setup Guide

## Overview
The Payroll module provides comprehensive pay slip generation, management, and PDF export functionality for the HRM system. It supports both monthly and weekly pay periods, batch processing, and role-based access control.

## Features
- ✅ **Pay Slip Generation**: Single and batch generation for employees
- ✅ **PDF Export**: Branded PDF pay slips with company information
- ✅ **Period Management**: Support for monthly and weekly pay periods
- ✅ **Salary Calculations**: Automatic calculation of gross, deductions, and net salary
- ✅ **Override Support**: Custom salary adjustments per employee
- ✅ **Batch Processing**: Queue-based processing for large organizations
- ✅ **Audit Logging**: Complete audit trail for all payroll actions
- ✅ **Role-based Access**: System Admin and HR Manager full access, Supervisor view-only
- ✅ **Search & Filter**: Advanced filtering and search capabilities
- ✅ **Status Management**: Track pending and paid pay slips

## Database Schema

### pay_slips Table
```sql
- id (bigIncrements)
- employee_id (foreignId -> employees) — indexed
- salary_structure_id (foreignId -> salary_structures) — nullable
- period_type (enum: month, week)
- period_month (tinyInteger nullable) — 1-12 for monthly
- period_year (smallInteger nullable)
- period_week_start (date nullable) — for weekly
- period_week_end (date nullable) — for weekly
- basic (decimal(12,2))
- allowances (json) — {"House Rent": 10000, "Transport": 2000}
- deductions (json) — {"Tax": 1500, "Insurance": 500}
- gross_salary (decimal(12,2))
- net_salary (decimal(12,2))
- status (enum: Pending, Paid) default Pending
- generated_at (timestamp)
- paid_at (timestamp nullable)
- generated_by (foreignId -> users)
- pdf_path (string nullable) — storage path to generated PDF
- meta (json nullable) — extra data, regeneration info
- softDeletes()
- timestamps
```

## Installation & Setup

### 1. Dependencies
The following packages are required and should already be installed:
```bash
composer require barryvdh/laravel-dompdf
```

### 2. Database Migration
Run the migration to create the pay_slips table:
```bash
php artisan migrate
```

### 3. Queue Configuration
For batch processing, configure your queue system:

**Environment Variables (.env):**
```env
QUEUE_CONNECTION=database
# or QUEUE_CONNECTION=redis for production
```

**Run Queue Worker:**
```bash
php artisan queue:work
```

### 4. Storage Configuration
Ensure storage is properly linked:
```bash
php artisan storage:link
```

### 5. PDF Storage
PDFs are stored in: `storage/app/pay_slips/{year}/{month}/`

## API Endpoints

### Pay Slip Management
- `GET /api/employment/pay-slips` - List pay slips with filtering
- `POST /api/employment/pay-slips` - Generate single pay slip
- `POST /api/employment/pay-slips/generate-batch` - Generate batch pay slips
- `GET /api/employment/pay-slips/{id}` - View specific pay slip
- `GET /api/employment/pay-slips/{id}/download` - Download PDF
- `PUT /api/employment/pay-slips/{id}/status` - Update status (mark as paid)
- `GET /api/employment/pay-slips/employees` - Get employees for generation

### Authentication & Authorization
All endpoints require `auth:sanctum` middleware. Role-based access:
- **System Admin & HR Manager**: Full access to all operations
- **Supervisor**: View-only access to pay slip history

## Usage Guide

### 1. Generate Pay Slips

#### Single Pay Slip Generation
```javascript
POST /api/employment/pay-slips
{
  "employee_id": 1,
  "period_type": "month",
  "period_month": 9,
  "period_year": 2025,
  "overrides": {
    "basic": 50000,
    "allowances": {"Bonus": 5000},
    "deductions": {"Advance": 2000}
  },
  "generate_pdf": true
}
```

#### Batch Pay Slip Generation
```javascript
POST /api/employment/pay-slips/generate-batch
{
  "employee_ids": [1, 2, 3, 4, 5],
  "period_type": "month",
  "period_month": 9,
  "period_year": 2025,
  "overrides": {
    "allowances": {"Festival Bonus": 10000}
  },
  "generate_pdf": true,
  "skip_existing": true
}
```

### 2. Weekly Pay Period
```javascript
{
  "period_type": "week",
  "period_week_start": "2025-09-16",
  "period_week_end": "2025-09-22"
}
```

### 3. Filter Pay Slips
```javascript
GET /api/employment/pay-slips?employee_id=1&department_id=2&status=Paid&search=john
```

## Frontend Pages

### 1. Generate Pay Slips (`/payroll/payslips/generate`)
- Period selection (monthly/weekly)
- Employee selection with department filtering
- Salary overrides configuration
- Batch generation with progress tracking

### 2. Pay Slip History (`/payroll/payslips`)
- Advanced filtering and search
- Paginated pay slip listing
- Download PDF functionality
- Status management (mark as paid)
- Regeneration capabilities

## Business Logic

### Salary Calculation Flow
1. **Basic Salary**: From salary structure or override
2. **Allowances**: Sum of structure allowances + overrides
3. **Gross Salary**: Basic + Total Allowances
4. **Deductions**: Sum of structure deductions + overrides
5. **Net Salary**: Gross - Total Deductions (minimum 0)

### Pay Slip Generation Process
1. Validate employee has assigned salary structure
2. Calculate salary components using PaySlipGenerator service
3. Create PaySlip record in database
4. Generate PDF using PdfGenerator service
5. Store PDF in organized directory structure
6. Log all actions in UserLog for audit trail

### Batch Processing
- Large batch operations are queued using Laravel Jobs
- Progress tracking through job status
- Error handling and retry mechanisms
- Background PDF generation for performance

## PDF Template

The PDF template (`resources/views/payroll/payslip.blade.php`) includes:
- Company branding and logo
- Employee information
- Detailed salary breakdown
- Professional styling with Tailwind CSS
- Signature sections
- Status indicators

## Security & Permissions

### Role-based Access Control
- **System Admin**: Full access to all payroll functions
- **HR Manager**: Full access to payroll management
- **Supervisor**: View-only access to pay slip history

### Data Protection
- All PDF downloads are logged
- Sensitive salary information is protected
- Audit trail for all payroll actions
- Soft deletes for data retention

## Error Handling

### Common Error Scenarios
1. **Employee without salary structure**: Skip or return error
2. **Duplicate pay slip**: Prevent or allow regeneration
3. **Negative net salary**: Prevented with validation
4. **PDF generation failure**: Logged and retried
5. **Queue processing errors**: Comprehensive error logging

### Validation Rules
- Period data validation (month 1-12, year 2020-2030)
- Employee existence validation
- Salary structure assignment validation
- JSON format validation for overrides

## Performance Considerations

### Optimization Strategies
1. **Queue Processing**: Background job processing for large batches
2. **PDF Caching**: Generated PDFs are stored and reused
3. **Database Indexing**: Optimized indexes for common queries
4. **Pagination**: Efficient data loading for large datasets

### Monitoring
- Queue job monitoring
- PDF generation success rates
- Database performance metrics
- User action logging

## Troubleshooting

### Common Issues

1. **PDF Generation Fails**
   - Check storage permissions
   - Verify dompdf installation
   - Check queue worker status

2. **Queue Jobs Not Processing**
   - Ensure queue worker is running
   - Check QUEUE_CONNECTION setting
   - Monitor failed jobs table

3. **Permission Errors**
   - Verify role middleware configuration
   - Check user admin_type values
   - Ensure proper authentication

4. **Database Errors**
   - Check migration status
   - Verify foreign key constraints
   - Check unique constraint violations

### Debug Commands
```bash
# Check queue status
php artisan queue:work --verbose

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Check storage link
php artisan storage:link

# Clear cache
php artisan cache:clear
php artisan config:clear
```

## Future Enhancements

### Planned Features
- [ ] Email delivery of pay slips
- [ ] Advanced reporting and analytics
- [ ] Integration with accounting systems
- [ ] Mobile app support
- [ ] Multi-currency support
- [ ] Tax calculation automation
- [ ] Direct deposit integration

### Configuration Options
- Custom PDF templates per company
- Configurable salary calculation rules
- Flexible period definitions
- Custom audit log formats

## Support

For technical support or feature requests, please refer to the main project documentation or contact the development team.

---

**Version**: 1.0.0  
**Last Updated**: January 2025  
**Compatibility**: Laravel 10+, Vue 3, PHP 8.1+
