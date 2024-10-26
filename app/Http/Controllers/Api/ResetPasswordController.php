<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\VonageService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ResetPasswordController extends Controller
{
    protected $vonageService;

    public function __construct(VonageService $vonageService)
    {
        $this->vonageService = $vonageService;
    }
    public function requestPasswordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'to' => 'required|string',
        ]);

        $user = User::where('email', $request->input('email'))->first();
        $token = Str::random(60);

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        $resetLink = url('password-reset', $token);

        $messageText = "Use this link to reset your password: $resetLink";
        $this->vonageService->sendSMS($request->input('to'), env('VONAGE_BRAND_NAME'), $messageText);

        return response()->json(['message' => 'Password reset link sent successfully.']);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|confirmed', 
        ]);

        $reset = DB::table('password_resets')->where('token', $request->input('token'))->first();

        if (!$reset || $reset->email !== $request->input('email')) {
            return response()->json(['message' => 'Invalid token or email.'], 400);
        }

        // Update the user's password
        $user = User::where('email', $reset->email)->first();
        $user->password = bcrypt($request->input('password'));
        $user->save();

        // Optionally, delete the token
        DB::table('password_resets')->where('token', $request->input('token'))->delete();

        return response()->json(['message' => 'Password has been reset successfully.']);
    }


}
