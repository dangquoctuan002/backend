<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Đăng ký người dùng mới
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'account' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Hash password before saving
        $hashedPassword = bcrypt($request->password);

        // Create new user record
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->account = $request->account;
        $user->password = $hashedPassword;
        $user->save();

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
        // try {
        //     // Validation
        //     $request->validate([
        //         'name' => 'required|string|max:255',
        //         'email' => 'required|string|email|max:255|unique:users',
        //         'account' => 'required|string|max:255|unique:users',
        //         'password' => 'required|string|min:8',
        //     ]);

        //     $user = new User();
        //     $user->name = $request->name;
        //     $user->email = $request->email;
        //     $user->account = $request->account;
        //     $user->password = Hash::make($request->password);
        //     $user->save();

        //     // // Create user
        //     // $user = User::create([
        //     //     'name' => $request->name,
        //     //     'email' => $request->email,
        //     //     'phone' => $request->phone,
        //     //     'address' => $request->address,
        //     //     'image' => $request->image,
        //     //     'gender' => $request->gender,
        //     //     'birthday' => $request->birthday,
        //     //     'account' => $request->account,
        //     //     'password' => Hash::make($request->password),
        //     // ]);

        //     // Return success response
        //     return response()->json(['status' => 1, 'message' => "created successful"], 201);
        // } catch (\Illuminate\Validation\ValidationException $e) {
        //     // Handle validation errors
        //     return response()->json(['status' => 0, 'message' => $e->validator->errors()->first()], 422);
        // } catch (\Exception $e) {
        //     // Handle other exceptions
        //     \Log::error('User creation failed: ' . $e->getMessage());
        //     return response()->json(['status' => 0, 'message' => 'User creation failed'], 500);
        // }
    }

    // Đăng nhập người dùng
    public function login(Request $request)
    {
        $request->validate([
            'account' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('account', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return response()->json(['user' => $user, 'message' => 'Login successful']);
        }

        return response()->json(['message' => 'Invalid account or password'], 401);
    }

    // Quên mật khẩu
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email not found'], 404);
        }

        $token = Str::random(60);
        $user->remember_token = $token;
        $user->save();

        Mail::send('emails.reset-password', ['token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password Notification');
        });

        return response()->json(['message' => 'We have emailed your password reset link!']);
    }
}

