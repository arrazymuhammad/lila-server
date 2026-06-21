<?php

namespace App\Http\Controllers;

use App\Models\FindingCategory;
use Illuminate\Http\Request;

class FindingCategoryController extends Controller
{
    public function index()
    {
        $categories = FindingCategory::orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:finding_categories,name',
        ], [
            'name.unique' => 'Nama kategori sudah digunakan.',
        ]);

        FindingCategory::create($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function destroy(FindingCategory $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
