<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FindingCategory;
use Illuminate\Support\Facades\Cache;

/** TASK-118 + TASK-154: Categories endpoint with cache */
class CategoryController extends Controller
{
    public function index()
    {
        $categories = Cache::remember('categories_master', 3600, function () {
            return FindingCategory::orderBy('name')->pluck('name');
        });

        return response()->json([
            'categories' => $categories,
            'count' => $categories->count(),
        ]);
    }
}
