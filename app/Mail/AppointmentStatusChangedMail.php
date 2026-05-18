<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Appointment $appointment;
    public string $oldStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment, string $oldStatus)
    {
        $this->appointment = $appointment;
        $this->oldStatus = $oldStatus;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Status Appointment Berubah - ' . ucfirst($this->appointment->status),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.appointment-status-changed-mail',
            with: [
                'appointment' => $this->appointment->load('patient', 'doctor'),
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->appointment->status,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
