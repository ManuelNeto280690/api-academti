<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'lessons' => 'required|array',
            'lessons.*.id' => 'required|uuid|exists:lessons,id',
            'lessons.*.order' => 'required|integer',
            'lessons.*.module_id' => 'nullable|uuid|exists:modules,id',
        ]);

        foreach ($validated['lessons'] as $lessonData) {
            $update = ['order' => $lessonData['order']];
            if (isset($lessonData['module_id'])) {
                $update['module_id'] = $lessonData['module_id'];
            }
            \App\Models\Lesson::where('id', $lessonData['id'])->update($update);
        }

        return response()->json(['message' => 'Ordem das aulas atualizada.']);
    }

    public function index(Module $module)
    {
        return response()->json($module->lessons()->orderBy('order')->get());
    }

    public function store(Request $request, Module $module)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:video,live,text,presencial',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'is_preview' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'meeting_platform' => 'nullable|string',
            'meeting_link' => 'nullable|url',
            'meeting_id' => 'nullable|string',
            'meeting_password' => 'nullable|string',
        ]);

        if (!isset($validated['order'])) {
            $validated['order'] = $module->lessons()->max('order') + 1;
        }

        $lesson = $module->lessons()->create($validated);

        return response()->json($lesson, 201);
    }

    public function show(Lesson $lesson)
    {
        return response()->json($lesson);
    }

    public function update(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:video,live,text,presencial',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'is_preview' => 'nullable|boolean',
            'order' => 'required|integer',
            'meeting_platform' => 'nullable|string',
            'meeting_link' => 'nullable|url',
            'meeting_id' => 'nullable|string',
            'meeting_password' => 'nullable|string',
        ]);

        $lesson->update($validated);

        return response()->json($lesson);
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return response()->json(null, 204);
    }
}
