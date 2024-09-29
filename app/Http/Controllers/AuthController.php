<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function index()
    {

    }

    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password'])
        ]);
        // Generate a token for the user

        return response()->json([
            'message' => 'User created successfully',
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            abort(response()->json([
                'email' => ['The provided credentials are incorrect.'],
            ], 422));
        }

        $token = $user->createToken('auth_token',[])->plainTextToken;
//        dd(Auth::user());
        return response()->json([
            'message' => 'User logged in successfully',
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'User logged out successfully',
        ], 200);
    }

    public function forgot_password(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The provided email is not registered.'],
            ]);
        }

        // Generate a token for the user

        return response()->json([
            'message' => 'Password reset link sent successfully',
        ], 200);
    }

    public function getResetToken(Request $request)
    {
        $request->validate(['password' => 'required']);

        if (Hash::check($request->password, $request->user()->password)) {
            $request->user()->createToken('auth_token', ['password-update'])->plainTextToken;
            return response()->json(['message' => 'Access token generated successfully'], 200);
        }

        return response()->json(['message' => 'Password does not match'], 401);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed',
        ]);
        $request->user()->update([
            'password' => Hash::make($request->password)
        ]);
        return response()->json(['message' => 'Password reset successfully'], 200);
    }
}
