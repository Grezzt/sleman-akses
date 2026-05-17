<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
  public function __construct(private readonly CategoryService $categoryService) {}

  public function index(): View
  {
    $categories = $this->categoryService->getAll();

    return view('admin.categories.index', [
      'categories' => $categories,
    ]);
  }

  public function store(Request $request): RedirectResponse
  {
    $data = $request->validate([
      'facility_name' => ['required', 'string', 'max:120'],
      'icon_marker' => ['nullable', 'string', 'max:255'],
    ]);

    $this->categoryService->create($data);

    return back()->with('success', 'Kategori berhasil ditambahkan.');
  }

  public function update(Request $request, int $id): RedirectResponse
  {
    $data = $request->validate([
      'facility_name' => ['required', 'string', 'max:120'],
      'icon_marker' => ['nullable', 'string', 'max:255'],
    ]);

    $this->categoryService->update($id, $data);

    return back()->with('success', 'Kategori berhasil diperbarui.');
  }

  public function destroy(int $id): RedirectResponse
  {
    $this->categoryService->delete($id);

    return back()->with('success', 'Kategori berhasil dihapus.');
  }
}
