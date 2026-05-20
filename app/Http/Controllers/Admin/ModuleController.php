<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Certification;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function certificationModules(Certification $certification)
    {
        return response()->json($certification->modules()->with(['lessons', 'quizzes', 'materials'])->orderBy('order')->get());
    }

    public function storeCertificationModule(Request $request, Certification $certification)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'nullable|integer',
        ]);

        if (!isset($validated['order'])) {
            $validated['order'] = $certification->modules()->max('order') + 1;
        }

        $module = $certification->modules()->create($validated);

        return response()->json($module, 201);
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'modules' => 'required|array',
            'modules.*.id' => 'required|uuid|exists:modules,id',
            'modules.*.order' => 'required|integer',
        ]);

        foreach ($validated['modules'] as $modData) {
            \App\Models\Module::where('id', $modData['id'])->update(['order' => $modData['order']]);
        }

        return response()->json(['message' => 'Ordem dos módulos atualizada.']);
    }

    public function index(Course $course)
    {
        return response()->json($course->modules()->with(['lessons', 'quizzes', 'materials'])->orderBy('order')->get());
    }

    public function store(Request $request, Course $course)
    {
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

    public function show(Module $module)
    {
        return response()->json($module->load('lessons'));
    }

    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'required|integer',
        ]);

        $module->update($validated);

        return response()->json($module);
    }

    public function destroy(Module $module)
    {
        $module->delete();
        return response()->json(null, 204);
    }
}
