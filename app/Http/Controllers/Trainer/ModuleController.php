<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(Request $request, Course $course)
    {
        if ($course->trainer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($course->modules()->with(['lessons', 'quizzes', 'materials'])->orderBy('order')->get());
    }

    public function store(Request $request, Course $course)
    {
        if ($course->trainer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'nullable|integer',
        ]);

        if (!isset($validated['order'])) {
            $validated['order'] = $course->modules()->max('order') + 1;
        }

        $module = $course->modules()->create($validated);

        return response()->json($module, 201);
    }

    public function update(Request $request, Module $module)
    {
        // Check ownership through course
        $course = $module->course;
        if (!$course || $course->trainer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'required|integer',
        ]);

        $module->update($validated);

        return response()->json($module);
    }

    public function destroy(Request $request, Module $module)
    {
        $course = $module->course;
        if (!$course || $course->trainer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $module->delete();
        return response()->json(null, 204);
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'modules' => 'required|array',
            'modules.*.id' => 'required|uuid|exists:modules,id',
            'modules.*.order' => 'required|integer',
        ]);

        // Security check: ensure all modules belong to this trainer's courses
        foreach ($validated['modules'] as $modData) {
            $mod = \App\Models\Module::find($modData['id']);
            if ($mod && $mod->course->trainer_id === $request->user()->id) {
                $mod->update(['order' => $modData['order']]);
            }
        }

        return response()->json(['message' => 'Ordem dos módulos atualizada.']);
    }
}
