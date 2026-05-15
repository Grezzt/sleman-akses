<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $userId = $this->user()?->id;

    return [
      'full_name' => ['sometimes', 'string', 'max:255'],
      'email' => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($userId)],
      'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
    ];
  }
}
