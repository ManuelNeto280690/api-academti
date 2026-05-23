<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogPost;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    public function index()
    {
        $posts = BlogPost::with(['category', 'tags', 'author'])->orderBy('id', 'desc')->get();
        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_posts,slug',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'cover_image' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'tags' => 'array',
            'tags.*' => 'exists:blog_tags,id'
        ]);

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $validated['author_id'] = $request->user()->id;

        $post = BlogPost::create($validated);

        if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return response()->json($post->load(['category', 'tags']), 201);
    }

    public function show(BlogPost $blogPost)
    {
        return response()->json($blogPost->load(['category', 'tags', 'author']));
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $validated = $request->validate([
            'title' => 'string|max:255',
            'slug' => 'string|max:255|unique:blog_posts,slug,' . $blogPost->id,
            'excerpt' => 'nullable|string',
            'content' => 'string',
            'cover_image' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'status' => 'in:draft,published',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'tags' => 'array',
            'tags.*' => 'exists:blog_tags,id'
        ]);

        if (isset($validated['status']) && $validated['status'] === 'published' && $blogPost->status !== 'published') {
            $validated['published_at'] = now();
        }

        $blogPost->update($validated);

        if (isset($validated['tags'])) {
            $blogPost->tags()->sync($validated['tags']);
        }

        return response()->json($blogPost->load(['category', 'tags']));
    }

    public function destroy(BlogPost $blogPost)
    {
        $blogPost->delete();
        return response()->json(['message' => 'Artigo eliminado com sucesso.']);
    }
}
