<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SmsChefController extends Controller
{
protected $smsChefService;

    public function __construct(SMSChefService $smsChefService)
    {
        $this->smsChefService = $smsChefService;
    }

    public function sendSms()
    {
        // $numbers = "+639614901967,+639123456789,+639123456789";
        $numbers = "+639614901967";
        $message = "Hello World!";

        $result = $this->smsChefService->sendSMS($numbers, $message);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']]);
        }

        return response()->json(['message' => 'SMS sent successfully', 'result' => $result]);
    }
}
