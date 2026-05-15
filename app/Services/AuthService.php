<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AuthService
{
    public function __construct(private readonly UserRepositoryInterface $users) {}

    public function register(array $data): array
    {
        try {
            $data['password'] = Hash::make($data['password']);

            $user = $this->users->create($data);
            $token = $user->createToken('api')->plainTextToken;

            return ['user' => $user, 'token' => $token];
        } catch (Throwable $e) {
            if ($e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Failed to register user.', 0, $e);
        }
    }

    public function login(array $credentials): array
    {
        try {
            if (!Auth::attempt($credentials)) {
                throw new \RuntimeException('Invalid credentials.');
            }

            /** @var User|null $user */
            $user = Auth::user();

            if (!$user) {
                throw new \RuntimeException('Authenticated user not found.');
            }

            if (!in_array($user->role, ['user', 'admin'], true)) {
                throw new \RuntimeException('User role is invalid.');
            }

            $token = $user->createToken('api')->plainTextToken;

            return ['user' => $user, 'token' => $token];
        } catch (Throwable $e) {
            if ($e instanceof \RuntimeException) {
                throw $e;
            }

            throw new \RuntimeException('Failed to login.', 0, $e);
        }
    }

    public function logout(User $user): void
    {
        try {
            $tokenId = $user->currentAccessToken()?->id;

            if ($tokenId === null) {
                return;
            }

            $user->tokens()->whereKey($tokenId)->delete();
        } catch (Throwable $e) {
            throw new \RuntimeException('Failed to logout.', 0, $e);
        }
    }
}
