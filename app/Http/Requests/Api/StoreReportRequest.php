<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'latitude' => ['required', 'numeric', 'between:-90,90'],
      'longitude' => ['required', 'numeric', 'between:-180,180'],
      'photo_url' => ['required', 'string'],
      'categories' => ['required', 'array', 'min:1'],
      'categories.*.id' => ['required', 'integer', 'exists:facility_categories,id'],
      'categories.*.available' => ['required', 'boolean'],
    ];
  }
}
