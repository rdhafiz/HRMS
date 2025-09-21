<?php

namespace App\Mail;

use App\Models\EmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class EmailNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public EmailNotification $emailNotification;

    /**
     * Create a new message instance.
     */
    public function __construct(EmailNotification $emailNotification)
    {
        $this->emailNotification = $emailNotification;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailNotification->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
            with: [
                'emailNotification' => $this->emailNotification,
                'body' => $this->emailNotification->body ?: '<p>No content</p>',
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        
        if ($this->emailNotification->attachments && is_array($this->emailNotification->attachments)) {
            foreach ($this->emailNotification->attachments as $attachment) {
                // Check if the file exists in storage
                if (Storage::disk('public')->exists($attachment['path'])) {
                    $attachments[] = Attachment::fromStorageDisk('public', $attachment['path'])
                        ->as($attachment['original_name'])
                        ->withMime($attachment['mime_type']);
                } else {
                    // Log missing file for debugging
                    \Log::warning('Attachment file not found: ' . $attachment['path']);
                }
            }
        }

        return $attachments;
    }
}
