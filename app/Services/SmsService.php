<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsService
{
    private $apiUrl = "https://www.cloud.smschef.com/api/send/sms.bulk";

    public function sendSms($phone, $message, $sim = 1, $priority = 1)
    {
        $payload = [
            "secret" => env('SMSCHEF_API_SECRET'),
            "mode" => "devices",
            "campaign"=> "bulk test",
            "device" => env('SMSCHEF_DEVICE_ID'),
            "sim" => $sim,
            "priority" => $priority,
            "numbers" => $phone,
            "message" => $message,
        ];

        // Use Laravel's HTTP client
        $response = Http::asForm()->post($this->apiUrl, $payload);

        if ($response->successful()) {
            return $response->json();
        }

        // Handle error responses
        return [
            'success' => false,
            'error' => $response->status(),
            'message' => $response->body(),
        ];
    }
}
