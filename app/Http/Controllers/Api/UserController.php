<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

    public function register(Request $request){

        $userDetails = $request->validate([
            'username'=> ['required','string','max:255'],
            'email'=> ['required','email','unique:users,email'],
            'type'=> ['required','integer'],
            'password'=> ['required','string','max:255']
        ]);

        $userDetails['password'] = bcrypt($userDetails['password']);

        try {
            $user = User::create($userDetails);
            return response()->json([
                'quack'=> true,
                'message' => 'User registered successfully',
                // 'user' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'quack'=> false,
                'message' => 'User registration failed',
                'quack' => $e->getMessage()
            ], 500);
        } 
    }

    public function login(Request $request){

        $userDetails = $request->validate([
            'username'=> ['required'],
            // 'email'=> ['required','email'],
            'password'=> ['required']
        ]);

        if (auth()->attempt(['username'=> $userDetails['username'],'password'=>$userDetails['password']]))
        {
            $user = auth()->user();
            return response()->json([
                'quack' => true,
                'message'=> 'Login Success',
                'user' => $user
            ], 200);
        }
        else
        {
            return response()->json([
                'quack'=>false,
                'message' => 'Username or password invalid'
            ], 401);
        }
    }
}
