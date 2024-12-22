<?php

namespace App\Http\Controllers\Api;

use App\Services\SmsService;
use App\Models\User; // Add this line to import the User model
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SmsController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function sendBulkSms(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        // Retrieve active users with phone numbers
        $users = User::where('status', 1)->whereNotNull('phone')->get();

        $responses = [];
        
        // Loop through each user and send SMS
        foreach ($users as $user) {
            $response = $this->smsService->sendSms(
                $user->phone,
                $request->message
            );
            $responses[] = [
                'user_id' => $user->id,
                'phone' => $user->phone,
                'response' => $response
            ];
        }

        // Return responses for each sent SMS
        return response()->json($responses);
    }
}
