<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $user = User::where('email', $validatedData['email'])->first();

        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    public function register(Request $request)
    {
          $validatedData = $request->validate([
              'name' => 'required|string|max:255',
              'email' => 'required|string|email|max:255|unique:users',
              'password' => 'required|string|min:8',
              'phone_number' => 'required|string|max:20',
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'phone_number' => $validatedData['phone_number'],
            ]);

            // log in the user after registration
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
            ], 201);
    }

    // the user model will need to be extended for this
    public function getUsers() {
        try {
            $users = User::all();
            return response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }
}
