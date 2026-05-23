<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogTag;

class BlogTagController extends Controller
{
    public function index()
    {
        return response()->json(BlogTag::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_tags,slug',
        ]);

        $tag = BlogTag::create($validated);
        return response()->json($tag, 201);
    }

    public function show(BlogTag $blogTag)
    {
        return response()->json($blogTag);
    }

    public function update(Request $request, BlogTag $blogTag)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'slug' => 'string|max:255|unique:blog_tags,slug,' . $blogTag->id,
        ]);

        $blogTag->update($validated);
        return response()->json($blogTag);
    }

    public function destroy(BlogTag $blogTag)
    {
        $blogTag->delete();
        return response()->json(['message' => 'Tag eliminada com sucesso.']);
    }
}
