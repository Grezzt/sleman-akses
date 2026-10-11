<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Spatial\DbscanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpatialAnalysisController extends Controller
{
    public function __construct(private readonly DbscanService $dbscanService) {}

    /**
     * Tampilkan halaman dasbor analisis spasial clustering DBSCAN dan blank spot 17 Kapanewon.
     */
    public function index(Request $request): View
    {
        $eps = (float) $request->query('eps', 800.0);
        $minPts = (int) $request->query('min_pts', 3);

        $analysis = $this->dbscanService->run($eps, $minPts);

        return view('admin.spatial.index', [
            'analysis' => $analysis,
            'epsilon' => $eps,
            'minPts' => $minPts,
        ]);
    }

    /**
     * Endpoint API asynchronous untuk kalkulasi dinamis saat slider parameter digeser.
     */
    public function data(Request $request): JsonResponse
    {
        $eps = (float) $request->query('eps', 800.0);
        $minPts = (int) $request->query('min_pts', 3);

        $analysis = $this->dbscanService->run($eps, $minPts);

        return response()->json([
            'success' => true,
            'data' => $analysis,
        ]);
    }
}
