<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index(Request $request, Module $module)
    {
        $course = $module->course;
        if (!$course || $course->trainer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($module->lessons()->orderBy('order')->get());
    }

    public function store(Request $request, Module $module)
    {
        $course = $module->course;
        if (!$course || $course->trainer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

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

    public function update(Request $request, Lesson $lesson)
    {
        $course = $lesson->module->course;
        if (!$course || $course->trainer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

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

    public function destroy(Request $request, Lesson $lesson)
    {
        $course = $lesson->module->course;
        if (!$course || $course->trainer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lesson->delete();
        return response()->json(null, 204);
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'lessons' => 'required|array',
            'lessons.*.id' => 'required|uuid|exists:lessons,id',
            'lessons.*.order' => 'required|integer',
            'lessons.*.module_id' => 'nullable|uuid|exists:modules,id',
        ]);

        foreach ($validated['lessons'] as $lessonData) {
            $lesson = \App\Models\Lesson::find($lessonData['id']);
            if ($lesson && $lesson->module->course->trainer_id === $request->user()->id) {
                $update = ['order' => $lessonData['order']];
                if (isset($lessonData['module_id'])) {
                    $update['module_id'] = $lessonData['module_id'];
                }
                $lesson->update($update);
            }
        }

        return response()->json(['message' => 'Ordem das aulas atualizada.']);
    }
}
