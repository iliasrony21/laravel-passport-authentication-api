<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
// Forgot Password Route
Route::post('/forgot', [ForgotPasswordController::class, 'forgot']);
// Reset Password
Route::post('/rest-password', [ForgotPasswordController::class, 'resetPassword']);


Route::middleware('auth:api')->group(function(){
// user
Route::get('/user', [UserController::class, 'user']);

});


// Protect additional routes with auth middleware as needed
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});