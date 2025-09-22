<?php

namespace App\Mail;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaveApplicationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public LeaveRequest $leaveRequest;

    /**
     * Create a new message instance.
     */
    public function __construct(LeaveRequest $leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Leave Application: ' . $this->leaveRequest->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.leave-application',
            with: [
                'leaveRequest' => $this->leaveRequest,
                'formattedDates' => $this->formatLeaveDates(),
            ]
        );
    }

    private function formatLeaveDates()
    {
        switch ($this->leaveRequest->date_type) {
            case 'single':
                return $this->leaveRequest->start_date->format('F d, Y');
            case 'range':
                return $this->leaveRequest->start_date->format('F d, Y') . ' to ' . $this->leaveRequest->end_date->format('F d, Y');
            case 'multiple':
                $dates = collect($this->leaveRequest->specific_dates)
                    ->map(function ($date) {
                        return \Carbon\Carbon::parse($date)->format('F d, Y');
                    })
                    ->join(', ');
                return $dates;
            default:
                return $this->leaveRequest->start_date->format('F d, Y');
        }
    }
}
