<?php

namespace App\Notifications;

use Vonage\Client;
use Vonage\SMS\Message\SMS;
use Illuminate\Bus\Queueable;
use Vonage\Client\Credentials\Basic;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Vonage\Client\Exception\Exception as VonageException;

class SmsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;
    protected $to;

    /**
     * Create a new notification instance.
     *
     * @param string $to
     * @param string $message
     */
    public function __construct(string $to, string $message)
    {
        $this->to = $to;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['vonage'];
    }

    /**
     * Send the SMS using Vonage.
     *
     * @return void
     */
    public function toVonage($notifiable)
    {
        $basic = new Basic(env('VONAGE_API_KEY'), env('VONAGE_API_SECRET'));
        $client = new Client($basic);

        try {
            $response = $client->sms()->send(
                new SMS($this->to, env('VONAGE_VIRTUAL_NUMBER'), $this->message)
            );

            // Check if the message was sent successfully
            if ($response->getStatus() !== 0) {
                \Log::error("Vonage SMS error: " . $response->getErrorText());
            }

            return $response;
        } catch (VonageException $e) {
            \Log::error("Vonage SMS exception: " . $e->getMessage());
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'message' => $this->message,
            'to' => $this->to,
        ];
    }
}
