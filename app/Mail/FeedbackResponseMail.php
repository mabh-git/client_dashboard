<?php

namespace App\Mail;

use App\Models\Feedback;
use App\Models\FeedbackResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedbackResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Le feedback original
     */
    public Feedback $feedback;

    /**
     * La réponse au feedback
     */
    public FeedbackResponse $response;

    /**
     * Create a new message instance.
     */
    public function __construct(Feedback $feedback, FeedbackResponse $response)
    {
        $this->feedback = $feedback;
        $this->response = $response;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Réponse à votre commentaire',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.feedback-response',
            with: [
                'feedback' => $this->feedback,
                'response' => $this->response,
                'clientName' => $this->feedback->name ?? 'Client', 
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}