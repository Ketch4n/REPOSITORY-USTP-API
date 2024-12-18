<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SMSChefService
{
    public function sendSMS($numbers, $message)
    {
        // Validate inputs
        if (empty($numbers) || empty($message)) {
            return ['error' => 'Numbers or message cannot be empty'];
        }

        // Prepare the message payload
        $payload = [
            "secret" => env('SMSCHEF_API_SECRET'), // Ensure this is set in your .env file
            "mode" => "devices",
            "numbers" => is_array($numbers) ? implode(',', $numbers) : $numbers,
            "device" => env('SMSCHEF_DEVICE_ID'), // Device ID from your SMSChef account
            "sim" => 1,
            "priority" => 1,
            "message" => $message
        ];

        // API endpoint
        $apiUrl = "https://www.cloud.smschef.com/api/send/sms.bulk";

        // Use cURL to send the request
        $cURL = curl_init($apiUrl);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_POST, true);
        curl_setopt($cURL, CURLOPT_POSTFIELDS, http_build_query($payload));
        $response = curl_exec($cURL);
        curl_close($cURL);

        // Decode the response
        $result = json_decode($response, true);

        // Log response for debugging
        Log::info('SMSChef API Response', ['response' => $response]);

        // Handle errors or return response
        if ($result) {
            return $result; // Return parsed JSON response
        } else {
            return [
                'error' => 'Unable to decode response',
                'raw_response' => $response
            ];
        }
    }
}
