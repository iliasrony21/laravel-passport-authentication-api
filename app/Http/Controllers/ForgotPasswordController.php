<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Mail\ForgotMail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function forgot(ForgotPasswordRequest $request)
    {
        $email = $request->email;

        if (User::where('email', $email)->doesntExist()) {
            return response([
                'message' => 'Email Invalid'
            ], 404);
        }

        $token = rand(10000, 100000); // You might want to use a more secure token generator for production use.

        try {
            // Check if the email already exists in the password_reset_tokens table
            $existingToken = DB::table('password_reset_tokens')->where('email', $email)->first();

            if ($existingToken) {
                // Update the existing token
                DB::table('password_reset_tokens')
                    ->where('email', $email)
                    ->update([
                        'token' => $token,
                        'created_at' => now(),
                    ]);
            } else {
                // Insert a new token
                DB::table('password_reset_tokens')->insert([
                    'email' => $email,
                    'token' => $token,
                    'created_at' => now(),
                ]);
            }

            // Send the reset email
            Mail::to($email)->send(new ForgotMail($token));

            return response([
                'message' => 'Reset Password Mail sent to your Email'
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // Reset Password method
    public function resetPassword(ResetPasswordRequest $request)
    {
        $email = $request->email;
        $token = $request->token;
        $password = Hash::make($request->password);

        // Check if the email and token exist together in the same record
        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$tokenData) {
            return response([
                'message' => "Invalid Email or Token"
            ], 401);
        }

        // Update the user's password
        User::where('email', $email)->update(['password' => $password]);

        // Delete the used token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return response([
            'message' => 'Password Changed Successfully'
        ], 200);
    }

}