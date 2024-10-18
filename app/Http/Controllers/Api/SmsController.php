<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; // Make sure this is included
use App\Notifications\SmsNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class SmsController extends Controller
{
    public function sendSms(Request $request)
    {
        // Validate the request
        $request->validate([
            'phone_number' => 'required|string',
            'message' => 'required|string',
        ]);

        // Get the phone number and message from the request
        $phoneNumber = $request->input('phone_number');
        $message = $request->input('message');

        try {
            // Send the SMS notification
            Notification::route('vonage', $phoneNumber)->notify(new SmsNotification($phoneNumber, $message));

            // Return a response
            return response()->json(['status' => 'SMS sent successfully!']);
        } catch (\Exception $e) {
            // Log the exception
            \Log::error("Error sending SMS: " . $e->getMessage());

            // Return an error response
            return response()->json(['status' => 'Error sending SMS', 'error' => $e->getMessage()], 500);
        }
    }

}
