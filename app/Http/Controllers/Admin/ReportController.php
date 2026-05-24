<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacilityCategory;
use App\Repositories\Contracts\ReportRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
  public function __construct(private readonly ReportRepositoryInterface $reports) {}

  public function index(Request $request): View
  {
    $status = $request->get('status');
    $validStatuses = ['pending', 'approved', 'rejected'];

    if (!in_array($status, $validStatuses, true)) {
      $status = null;
    }

    $reports = $this->reports->getAllPaginated([
      'status' => $status,
      'category_id' => $request->get('category_id'),
      'per_page' => $request->get('per_page'),
    ]);

    $categories = FacilityCategory::orderBy('facility_name')->get();

    return view('admin.reports.index', [
      'reports' => $reports,
      'status' => $status,
      'categories' => $categories,
    ]);
  }

  public function show(int $id): View
  {
    $report = $this->reports->findWithRelations($id);

    abort_if(!$report, 404);

    return view('admin.reports.show', [
      'report' => $report,
    ]);
  }
}
