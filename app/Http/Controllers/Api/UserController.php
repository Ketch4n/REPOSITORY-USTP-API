<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class UserController extends Controller
{

    public function index(Request $request)
    {
     
    
        $type = $request->query('type');

 
        if ($type) {
             $users = User::where('type', $type)->get();
        } else {

        $users = User::all();
        }

        // return UserResource::collection($users);





        // // $users = User::all();
        
        // $type = $request->query('type');

        // $query = User::query();

        // if ($type !== null) {
        //     $query->where('type', $type);
        // }

        // $users = $query->get();

        return response()->json([
            'quack' => true,
            'data' => $users,
        ], 200);
    }


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
