<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\EmailNotification;
use App\Mail\EmailNotificationMail;
use Illuminate\Support\Facades\Mail;

class TestEmailAttachment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-attachment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email attachment functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing email attachment functionality...');

        // Create a test file
        $testContent = 'This is a test attachment file.';
        $testPath = 'email-attachments/test-file.txt';
        
        // Store the test file
        Storage::disk('public')->put($testPath, $testContent);
        
        if (Storage::disk('public')->exists($testPath)) {
            $this->info('✓ Test file created successfully: ' . $testPath);
        } else {
            $this->error('✗ Failed to create test file');
            return;
        }

        // Create a test email notification with attachment
        $emailNotification = EmailNotification::create([
            'subject' => 'Test Email with Attachment',
            'body' => '<p>This is a test email with an attachment.</p>',
            'recipients' => [
                [
                    'employee_id' => 1,
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'department_id' => 1,
                    'department_name' => 'Test Department',
                ]
            ],
            'attachments' => [
                [
                    'original_name' => 'test-file.txt',
                    'path' => $testPath,
                    'mime_type' => 'text/plain',
                    'size' => strlen($testContent),
                ]
            ],
            'sender_id' => 1,
            'sent_at' => now(),
        ]);

        $this->info('✓ Test email notification created');

        // Test the Mailable
        try {
            $mailable = new EmailNotificationMail($emailNotification);
            $attachments = $mailable->attachments();
            
            $this->info('✓ Mailable created successfully');
            $this->info('Attachments count: ' . count($attachments));
            
            if (count($attachments) > 0) {
                $this->info('✓ Attachments processed successfully');
            } else {
                $this->warn('⚠ No attachments found');
            }
            
        } catch (\Exception $e) {
            $this->error('✗ Error creating Mailable: ' . $e->getMessage());
        }

        // Clean up
        Storage::disk('public')->delete($testPath);
        $emailNotification->delete();
        
        $this->info('✓ Test completed and cleaned up');
    }
}