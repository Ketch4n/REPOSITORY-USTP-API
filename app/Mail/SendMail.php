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
     *
     * @param array $details
     */
    public function __construct(array $details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('ustp.repository@gmail.com', 'USTP Repository')
                    ->to($this->details['email']) // Ensure you set the recipient email here
                    ->subject('New Project Added') // Set subject here for clarity
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

    /**
     * Build the HTML content for the email.
     *
     * @return string
     */
    private function buildEmailContent(): string
    {
        return "
            <html>
            <body>
                <h1>Hello, {$this->details['username']}</h1>
                <p>{$this->details['message']}</p>
                <p>Check out the project at this link: <a href='{$this->details['link']}'>{$this->details['link']}</a></p>
                <table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>
                    <thead>
                        <tr>
                            <th>Project Title</th>
                            <th>Project Type</th>
                            <th>Year Published</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{$this->details['title']}</td>
                            <td>{$this->details['type']}</td>
                            <td>{$this->details['year']}</td>
                        </tr>
                    </tbody>
                </table>
            </body>
            </html>
        ";
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name', // Consider if you need this since you are using html() above
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return []; // Add any attachments if necessary
    }
}
