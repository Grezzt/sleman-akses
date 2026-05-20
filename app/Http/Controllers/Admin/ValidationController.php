<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ReportRepositoryInterface;
use App\Services\ValidationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
  public function __construct(
    private readonly ValidationService $validationService,
    private readonly ReportRepositoryInterface $reports
  ) {}

  public function store(Request $request, int $reportId): RedirectResponse
  {
    $validated = $request->validate([
      'approval_status' => ['required', 'in:approved,rejected'],
      'evaluation_note' => ['nullable', 'string', 'max:1000'],
      'place_name' => ['required_if:approval_status,approved', 'string', 'max:150'],
      'address' => ['required_if:approval_status,approved', 'string', 'max:255'],
      'status' => ['required_if:approval_status,approved', 'in:active,inactive'],
      'facility_type' => ['required_if:approval_status,approved', 'string', 'max:100'],
    ]);

    $report = $this->reports->findWithRelations($reportId);

    abort_if(!$report || !$report->validation, 404);

    $this->validationService->validate(
      $report->validation,
      $validated,
      $request->user()
    );

    return redirect()
      ->route('admin.reports.index')
      ->with('success', 'Laporan berhasil divalidasi.');
  }
}
