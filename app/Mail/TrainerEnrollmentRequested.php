<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrainerEnrollmentRequested extends Mailable
{
    use Queueable, SerializesModels;

    public $trainer;
    public $student;
    public $courses;

    public function __construct(User $trainer, User $student, array $courses)
    {
        $this->trainer = $trainer;
        $this->student = $student;
        $this->courses = $courses;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Novo Pedido de Inscrição - CEFTIC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trainer_enrollment',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
