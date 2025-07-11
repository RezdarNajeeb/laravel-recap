<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
        //
    }

    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register($request->validated());

        $user = $result['user'];

        return response()->json([
            'message' => 'User created successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'token' => $result['token'],
            ]
        ], 201);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        if (!$result) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = $result['user'];

        return response()->json([
            'message' => 'User logged in successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'token' => $result['token'],
            ]
        ]);
    }
}
