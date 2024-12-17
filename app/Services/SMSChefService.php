<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class SMSChefService
{
    public function sendSMS($numbers, $message)
    {
        // Define the message payload
        $payload = [
            "secret" => env('SMSCHEF_API_SECRET'), // Use .env for your secret
            "mode" => "devices",
            "numbers" => $numbers, // Can be a comma-separated string or an array
         
            "device" => "00000000-0000-0000-d57d-f30cb6a89289", // Replace with your actual device ID
            "sim" => 1,
            "priority" => 1,
            "message" => $message
        ];

        // Send request to SMSChef API
        $response = Http::post('https://www.cloud.smschef.com/api/send/sms.bulk', $payload);

        // Check for success or failure
        if ($response->successful()) {
            return $response->json(); // Return the response as an array
        } else {
            // Log error or return custom error message
            return ['error' => 'Failed to send SMS', 'response' => $response->body()];
        }
    }
}
