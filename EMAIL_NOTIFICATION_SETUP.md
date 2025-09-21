# Email Notification Module Setup

This document provides setup instructions for the Email Notification Module in the HR Management System.

## Features Implemented

### Backend (Laravel 10)
- ✅ Email notifications migration and model
- ✅ EmailNotification Mailable class with attachment support
- ✅ EmailNotificationController with full CRUD API
- ✅ Email template with rich HTML support
- ✅ File upload handling for attachments
- ✅ Role-based access control (System Admin, HR Manager, Supervisor)

### Frontend (Vue 3 + Vite + Tailwind)
- ✅ TipTap rich text editor component
- ✅ File upload component with drag & drop
- ✅ Send Notification page with recipient selection
- ✅ Email History page with search and filters
- ✅ Responsive design with modern UI

## Database Setup

The migration has been created and should be run:

```bash
php artisan migrate
```

This creates the `email_notifications` table with the following structure:
- `id` - Primary key
- `subject` - Email subject
- `body` - Rich text content (HTML)
- `recipients` - JSON array of recipient data
- `attachments` - JSON array of attachment metadata
- `sender_id` - Foreign key to users table
- `sent_at` - Timestamp when email was sent
- `created_at`, `updated_at` - Laravel timestamps

## API Endpoints

### Email Notifications
- `GET /api/email-notifications` - List notifications with filters
- `POST /api/email-notifications` - Send new notification (Admin/HR only)
- `GET /api/email-notifications/{id}` - View notification details
- `GET /api/email-notifications/departments` - Get departments list
- `GET /api/email-notifications/employees` - Get employees list

### Authentication Required
All endpoints require authentication via Laravel Sanctum.

### Role Permissions
- **System Admin**: Full access (send + view history)
- **HR Manager**: Full access (send + view history)
- **Supervisor**: View history only (no sending)

## Frontend Routes

- `/email-notifications/send` - Send new notification (Admin/HR only)
- `/email-notifications/history` - View email history (All roles)

## Mail Configuration

Configure your mail settings in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@domain.com
MAIL_FROM_NAME="HR Management System"
```

## File Storage

Attachments are stored in `storage/app/public/email-attachments/`. Make sure to:

1. Create the storage link:
```bash
php artisan storage:link
```

2. Set proper permissions for the storage directory

## Usage

### Sending Notifications

1. Navigate to "Notifications" → "Send Notification"
2. Select recipient type:
   - **All Employees**: Sends to all employees
   - **Specific Department**: Select a department
   - **Selected Employees**: Choose specific employees
3. Enter subject and compose message using the rich text editor
4. Optionally attach files (PDF, DOC, images, etc.)
5. Click "Send Notification"

### Viewing History

1. Navigate to "Notifications" → "Email History"
2. Use filters to search by:
   - Subject text
   - Date range
   - Department
3. Click "View" to see full notification details

## Dependencies

### Backend
- Laravel 10
- Laravel Sanctum (authentication)
- Laravel Mail (email sending)

### Frontend
- Vue 3
- TipTap editor (@tiptap/vue-3, @tiptap/starter-kit, etc.)
- Tailwind CSS
- Axios

## Testing

To test the email functionality:

1. Set up a test email configuration
2. Create some test employees with valid email addresses
3. Send a test notification
4. Check the email history to verify it was recorded

## Troubleshooting

### Common Issues

1. **Emails not sending**: Check mail configuration and SMTP settings
2. **File upload errors**: Verify storage permissions and file size limits
3. **Permission errors**: Ensure user has correct role (System Admin or HR Manager)
4. **Rich text not displaying**: Check if TipTap editor is properly loaded

### Debug Mode

Enable debug mode in `.env` to see detailed error messages:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

## Security Considerations

- File uploads are validated for type and size
- Email content is sanitized but HTML is allowed for rich formatting
- Role-based access control prevents unauthorized access
- File attachments are stored securely in Laravel's storage system

## Future Enhancements

Potential improvements for future versions:
- Email templates and presets
- Scheduled email sending
- Email delivery status tracking
- Bulk recipient management
- Email analytics and reporting
- Integration with external email services
