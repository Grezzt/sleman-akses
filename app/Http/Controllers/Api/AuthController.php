<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class AuthController extends Controller
{
  public function __construct(private readonly AuthService $auth) {}

  public function register(RegisterRequest $request): JsonResponse
  {
    $payload = $this->auth->register($request->validated());

    return response()->json([
      'success' => true,
      'message' => 'Registrasi berhasil.',
      'data' => [
        'token' => $payload['token'],
        'user' => $this->formatUser($payload['user']),
      ],
    ], 201);
  }

  public function login(LoginRequest $request): JsonResponse
  {
    try {
      $payload = $this->auth->login($request->validated());

      return response()->json([
        'success' => true,
        'message' => 'Login berhasil.',
        'data' => [
          'token' => $payload['token'],
          'user' => $this->formatUser($payload['user']),
        ],
      ]);
    } catch (RuntimeException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Email atau password salah.',
        'errors' => null,
      ], 401);
    }
  }

  public function logout(Request $request): JsonResponse
  {
    $user = $request->user();

    if ($user) {
      $this->auth->logout($user);
    }

    return response()->json([
      'success' => true,
      'message' => 'Logout berhasil.',
      'data' => null,
    ]);
  }

  private function formatUser($user): array
  {
    return [
      'user_id' => $user->id,
      'full_name' => $user->full_name,
      'email' => $user->email,
      'role' => $user->role,
    ];
  }
}
