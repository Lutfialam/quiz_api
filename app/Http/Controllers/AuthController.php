<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\LoginUserRequest;
use App\Http\Requests\User\StoreUserRequest;

class AuthController extends Controller
{
    public function register(StoreUserRequest $request)
    {
        $userRepository = new UserRepository();
        $user = $userRepository->create($request);

        return response()->json([
            'user' => $user,
            'message' => 'User created successfully',
        ], 201);
    }

    public function login(LoginUserRequest $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'password',
                'message' => 'Password is incorrect',
            ], 401);
        }

        $tokenName = $request->type === 'mobile' ? $request->device_name : 'authToken';
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'user' => $user,
            'token_type' => 'Bearer',
            'access_token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
