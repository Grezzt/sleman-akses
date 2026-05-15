<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
  public function __construct(private readonly UserRepositoryInterface $users) {}

  public function show(): JsonResponse
  {
    $user = Auth::user();

    return response()->json([
      'success' => true,
      'message' => 'Berhasil mengambil profil.',
      'data' => [
        'user_id' => $user->id,
        'full_name' => $user->full_name,
        'email' => $user->email,
        'role' => $user->role,
      ],
    ]);
  }

  public function update(UpdateProfileRequest $request): JsonResponse
  {
    $user = Auth::user();
    $data = $request->validated();

    if (array_key_exists('password', $data) && $data['password']) {
      $data['password'] = Hash::make($data['password']);
    } else {
      unset($data['password']);
    }

    $user = $this->users->update($user, $data);

    return response()->json([
      'success' => true,
      'message' => 'Profil berhasil diperbarui.',
      'data' => [
        'user_id' => $user->id,
        'full_name' => $user->full_name,
        'email' => $user->email,
        'role' => $user->role,
      ],
    ]);
  }
}
