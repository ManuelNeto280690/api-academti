<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    /**
     * List materials for a specific model (Lesson or Course).
     */
    public function index(Request $request)
    {
        $request->validate([
            'materialable_id' => 'required|uuid',
            'materialable_type' => 'required|string|in:lesson,course,module'
        ]);

        $typeMap = [
            'lesson' => Lesson::class,
            'course' => Course::class,
            'module' => Module::class,
        ];

        $type = $typeMap[$request->materialable_type];
        
        $materials = Material::where('materialable_id', $request->materialable_id)
            ->where('materialable_type', $type)
            ->get();

        return response()->json($materials);
    }

    /**
     * Store a new material.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB Max
            'title' => 'required|string|max:255',
            'materialable_id' => 'required|uuid',
            'materialable_type' => 'required|string|in:lesson,course,module'
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            $typeMap = [
                'lesson' => Lesson::class,
                'course' => Course::class,
                'module' => Module::class,
            ];
            $type = $typeMap[$request->materialable_type];
            
            // Store file
            $path = $file->store('materials', 'public');
            
            $material = Material::create([
                'materialable_id' => $request->materialable_id,
                'materialable_type' => $type,
                'title' => $request->title,
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'type' => $file->getClientOriginalExtension()
            ]);

            return response()->json($material, 201);
        }

        return response()->json(['message' => 'Ficheiro não enviado.'], 400);
    }

    /**
     * Remove a material.
     */
    public function destroy(Material $material)
    {
        if (Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return response()->json(null, 204);
    }
}
