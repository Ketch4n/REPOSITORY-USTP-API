<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\VonageService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class VonageController extends Controller
{
    protected $vonageService;

    public function __construct(VonageService $vonageService)
    {
        $this->vonageService = $vonageService;
    }

    public function sendSMS(Request $request)
    {
        $request->validate([
            'to' => 'required|string',
            'message' => 'required|string',
        ]);

        $to = $request->input('to');
        $brandName = env('VONAGE_BRAND_NAME');
        $messageText = $request->input('message');

        $result = $this->vonageService->sendSMS($to, $brandName, $messageText);

        return response()->json(['message' => $result]);
    }
    
    public function sendPasswordResetToken(Request $request)
    {
    $request->validate([
        'to' => 'required|string',
        'email' => 'required|email|exists:users,email',
    ]);

    $user = User::where('email', $request->input('email'))->first();
    $token = Str::random(60); // Generate a unique token

    // Save the token in the password resets table (or your preferred method)
    DB::table('password_reset_tokens')->insert([
        'email' => $user->email,
        'token' => $token,
        'created_at' => now(),
    ]);

    // Create a reset link
    $resetLink = url('password/reset', $token);

    // Prepare and send the SMS
    $brandName = env('VONAGE_BRAND_NAME');
    $messageText = "Use this link to reset your password: $resetLink";

    $result = $this->vonageService->sendSMS($request->input('to'), $brandName, $messageText);

    return response()->json(['message' => 'Password reset link sent successfully.']);
    }

}
