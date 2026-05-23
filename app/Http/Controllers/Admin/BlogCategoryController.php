<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;

class BlogCategoryController extends Controller
{
    public function index()
    {
        return response()->json(BlogCategory::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_categories,slug',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $category = BlogCategory::create($validated);
        return response()->json($category, 201);
    }

    public function show(BlogCategory $blogCategory)
    {
        return response()->json($blogCategory);
    }

    public function update(Request $request, BlogCategory $blogCategory)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'slug' => 'string|max:255|unique:blog_categories,slug,' . $blogCategory->id,
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $blogCategory->update($validated);
        return response()->json($blogCategory);
    }

    public function destroy(BlogCategory $blogCategory)
    {
        $blogCategory->delete();
        return response()->json(['message' => 'Categoria eliminada com sucesso.']);
    }
}
