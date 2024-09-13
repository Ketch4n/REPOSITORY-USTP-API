<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    // public function build()
    // {
    //     return $this->subject('Test Email from Laravel')
    //                 ->from('ustp.repository@gmail.com', 'USTP Repository')
    //                 ->to($this->details['email'])
    //                 ->html($this->buildEmailContent()); // Use the email content method
    // }
    public function build()
    {
        return $this->from('ustp.repository@gmail.com', 'USTP Repository')
                    ->html($this->buildEmailContent());
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Project Added',
        );
    }
    private function buildEmailContent()
    {
        return "
            <html>
            <body>
                <h1>Hello, {$this->details['name']}</h1>
                <p>{$this->details['message']}</p>
                <p>Thank you!</p>
            </body>
            </html>
        ";
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
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
