<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // login method
    public function login(Request $request)
    {
        // Validate the request inputs
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            // Attempt login with credentials
            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();

                // Create a token using Laravel Passport
                $token = $user->createToken('app')->accessToken;

                // Return successful response with token and user details
                return response([
                    'message' => 'Login Successful',
                    'token' => $token,
                    'user' => $user,
                ], 200);
            } else {
                return response([
                    'message' => 'Invalid Email or Password',
                ], 401);
            }
        } catch (Exception $exception) {
            // Log the exception and return an error message
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    // Register method
    public function register(RegisterRequest $request)
    {

        try {
            $user =  User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' =>Hash::make($request->password),

            ]);
            $token = $user->createToken('app')->accessToken;

            return response([
                'message' => 'Registration Successfully Done',
                'token' => $token,
                'user' => $user,
            ], 200);
        } catch (Exception $exception) {
            // Log the exception and return an error message
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}