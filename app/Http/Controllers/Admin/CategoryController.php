<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('courses');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->latest()->get();
        
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'color' => 'required|string|max:7', // Hex format
            'icon' => 'nullable|string|max:50',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'color' => $validated['color'],
            'icon' => $validated['icon'],
        ]);

        return response()->json([
            'message' => 'Categoria criada com sucesso',
            'category' => $category,
        ], 201);
    }

    public function show(Category $category)
    {
        return response()->json($category->load('courses'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $category->id,
            'color' => 'sometimes|required|string|max:7',
            'icon' => 'nullable|string|max:50',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return response()->json([
            'message' => 'Categoria actualizada',
            'category' => $category,
        ]);
    }

    public function destroy(Category $category)
    {
        // Verificar se existem cursos associados
        if ($category->courses()->count() > 0) {
            return response()->json([
                'message' => 'Não é possível eliminar uma categoria que possui cursos associados.'
            ], 422);
        }

        $category->delete();
        return response()->json(['message' => 'Categoria removida com sucesso']);
    }
}
