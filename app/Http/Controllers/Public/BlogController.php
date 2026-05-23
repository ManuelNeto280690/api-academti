<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;

class BlogController extends Controller
{
    public function posts(Request $request)
    {
        $query = BlogPost::with(['category', 'tags', 'author'])
            ->where('status', 'published')
            ->orderBy('published_at', 'desc');

        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        return response()->json($query->paginate(9));
    }

    public function show($slug)
    {
        $post = BlogPost::with(['category', 'tags', 'author'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return response()->json($post);
    }

    public function categories()
    {
        return response()->json(BlogCategory::all());
    }

    public function tags()
    {
        return response()->json(BlogTag::all());
    }
}
