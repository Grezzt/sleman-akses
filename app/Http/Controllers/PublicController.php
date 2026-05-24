<?php

namespace App\Http\Controllers;

use App\Models\FacilityCategory;
use App\Models\Report;
use App\Services\CategoryService;
use App\Services\MapLocationService;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly MapLocationService $mapLocationService
    ) {}

    public function landing(): View
    {
        $categories = $this->categoryService->getAll();
        
        $stats = [
            'locations' => $this->mapLocationService->getPublished()->count(),
            'categories' => FacilityCategory::count(),
            'reports' => Report::count(),
        ];

        return view('public.landing', compact('categories', 'stats'));
    }

    public function about(): View
    {
        return view('public.about');
    }
}
