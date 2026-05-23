<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonial;

class TestimonialController extends Controller
{
    public function index()
    {
        return response()->json(Testimonial::orderBy('id', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|string',
            'likes' => 'integer',
            'comments' => 'integer',
            'date_string' => 'string',
            'verified' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $testimonial = Testimonial::create($validated);
        return response()->json($testimonial, 201);
    }

    public function show(Testimonial $testimonial)
    {
        return response()->json($testimonial);
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'role' => 'string|max:255',
            'company' => 'string|max:255',
            'content' => 'string',
            'image' => 'nullable|string',
            'likes' => 'integer',
            'comments' => 'integer',
            'date_string' => 'string',
            'verified' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $testimonial->update($validated);
        return response()->json($testimonial);
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return response()->json(['message' => 'Testemunho eliminado com sucesso.']);
    }
}
