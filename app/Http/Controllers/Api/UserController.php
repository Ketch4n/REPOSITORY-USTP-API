<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class UserController extends Controller
{

   public function showStatus(Request $request)
    {
        // Retrieve the 'type' and 'status' query parameters from the request
        $type = $request->query('type');
        $status = $request->query('status');

        // Start a query to filter users based on 'type' and 'status'
        $query = User::query();

        // Apply filters if 'type' and 'status' are provided
        if ($type) {
            $query->where('type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Fetch the filtered users
        $users = $query->get();

        // Return a JSON response with the users data
        return response()->json([
            'quack' => true,
            'data' => $users,
        ], 200);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(Request $request, User $user){

        $userDetails = $request->validate([
            'status'=> 'required|integer',
        ]);

        try {
            $user->update($userDetails);
            return response()->json([
                'quack'=> true,
                'message' => 'User updated successfully',
                // 'user' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'quack'=> false,
                'message' => 'User update failed',
                // 'quack' => $e->getMessage()
            ], 500);
        } 
    }


    public function register(Request $request){

        $userDetails = $request->validate([
            'username'=> ['required','string','max:255'],
            'email'=> ['required','email','unique:users,email'],
            'type'=> ['required','integer'],
            'status'=> ['required,integer'],
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
                // 'quack' => $e->getMessage()
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
                'message' => 'Username or password invalid',
                'user'=>[]
            ], 401);
        }
    }
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            'message'=> 'USER DELETED',
            'quack' => true,
        ],200);
    }

}
