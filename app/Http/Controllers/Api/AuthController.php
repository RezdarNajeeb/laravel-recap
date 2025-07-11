<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(protected AuthService $authService)
    {
        //
    }

    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register($request->validated());

        return $this->authResponse($result, 'User created successfully', 201);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return $this->success('User logged out successfully');
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        if (!$result) {
            return $this->error('Invalid credentials', 401);
        }

        return $this->authResponse($result);
    }

    private function authResponse(array $result, string $message = 'User logged in successfully', int $status = 200)
    {
        return $this->success($message, [
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], $status);
    }
}
