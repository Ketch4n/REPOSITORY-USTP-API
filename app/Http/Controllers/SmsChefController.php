<?php

namespace App\Http\Controllers;

use App\Services\SMSChefService;
use Illuminate\Http\Request;

class SmsChefController extends Controller
{
    protected $smsChefService;

    public function __construct(SMSChefService $smsChefService)
    {
        $this->smsChefService = $smsChefService;
    }

    public function sendSms(Request $request)
    {
        // Validate input
        $request->validate([
            'numbers' => 'required|string', // Comma-separated numbers
            'message' => 'required|string|max:160'
        ]);

        // Get data from request
        $numbers = $request->input('numbers');
        $message = $request->input('message');

        // Call the SMS service
        $result = $this->smsChefService->sendSMS($numbers, $message);

        // Handle response
        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 400);
        }

        return response()->json(['message' => 'SMS sent successfully', 'result' => $result]);
    }
}
