<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Mail\SendMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;


class EmailController extends Controller
{
    public function sendmail(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'message' => 'required|string'
        ]);

        // Extract data from the request
        $details = [
            'email' => $validated['email'],
            'name' => $validated['name'],
            'message' => $validated['message']
        ];

        // Send the email
        // Mail::to($validated['email'])->send(new SendMail($details));
        Mail::send(new SendMail($details));

        return response()->json(['message' => 'Email sent successfully!']);
    }

    public function sendmailTypeStatus(){
        $users = User::where('type', 2)
                     ->where('status', 1)
                     ->get();

        foreach ($users as $user) {
            $details = [
                'email' => $user->email,
                'username' => $user->username,
                'message' => 'New Project Added, check it now!'
            ];

        // Send email only to users who match both conditions
        Mail::to($user->email)->send(new SendMail($details));
        }

        return response()->json(['message' => 'Emails sent successfully!']);
    }

    // public function sendmailTypeStatus(){
    //     $users = User::where('status', 'active')->get();

    //      foreach ($users as $user) {
    //         $details = [
    //             'email' => $user->email,
    //             'name' => $user->name,
    //             'message' => 'New Project Added, check it now !'
    //         ];

    //         Mail::to($user->email)->send(new SendMail($details));
    //     }

    //     return response()->json(['message' => 'Emails sent successfully!']);
    // }
}
