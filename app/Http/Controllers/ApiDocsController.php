<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ApiDocsController extends Controller
{
  public function index(): View
  {
    return view('public.api-docs', [
      'specUrl' => route('api.docs.spec'),
    ]);
  }

  public function spec(): BinaryFileResponse
  {
    $path = base_path('docs/openapi.yaml');
    return response()->file($path, [
      'Content-Type' => 'application/yaml',
    ]);
  }
}
