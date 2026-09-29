<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
  public function index(Request $request): View
  {
    $query = User::query()->where('role', 'user')->withCount('reports');

    if ($request->filled('search')) {
        $search = $request->get('search');
        $query->where(function($q) use ($search) {
            $q->where('full_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    $users = $query->orderByDesc('created_at')->paginate(20);

    return view('admin.users.index', [
      'users' => $users,
    ]);
  }

  public function toggleStatus(Request $request, int $id)
  {
      $user = User::where('role', 'user')->findOrFail($id);
      
      $user->is_active = !$user->is_active;
      $user->save();

      if (!$user->is_active) {
          $user->tokens()->delete();
      }

      if ($request->wantsJson()) {
          return response()->json([
              'success' => true, 
              'message' => 'Status pengguna berhasil diubah.',
              'is_active' => $user->is_active
          ]);
      }

      return back()->with('success', 'Status pengguna berhasil diubah.');
  }
}
