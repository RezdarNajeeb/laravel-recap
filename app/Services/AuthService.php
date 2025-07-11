<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(protected UserRepository $users)
    {
        //
    }

    public function register(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
        $user = $this->users->create($data);

        return [
            'user' => $user,
            'token' => $user->createToken($user->email)->plainTextToken
        ];
    }

    public function logout(mixed $user): void
    {
        $user->tokens()->delete();
    }

    public function login(array $credentials): ?array
    {
        $user = $this->users->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        return [
            'user' => $user,
            'token' => $user->createToken($user->email)->plainTextToken
        ];
    }
}
