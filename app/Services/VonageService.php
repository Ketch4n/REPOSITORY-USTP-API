<?php
namespace App\Services;

use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;

class VonageService
{
    protected $client;

    public function __construct()
    {
        $basic  = new Basic(env('VONAGE_API_KEY'), env('VONAGE_API_SECRET'));
        $this->client = new Client($basic);
    }

    public function sendSMS($to, $brandName, $messageText)
    {
        $response = $this->client->sms()->send(new SMS($to, $brandName, $messageText));

        $message = $response->current();

        if ($message->getStatus() == 0) {
            return "The message was sent successfully";
        } else {
            return "The message failed with status: " . $message->getStatus();
        }
    }
}
