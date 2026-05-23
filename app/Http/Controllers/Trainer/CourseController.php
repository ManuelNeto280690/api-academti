<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::where('trainer_id', $request->user()->id)
            ->withCount(['enrollments as students_count'])
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($courses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'preco_normal' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'modalidade' => 'required|in:presencial,semi-presencial,ao-vivo,online',
        ]);

        $validated['trainer_id'] = $request->user()->id;
        $validated['status'] = 'pendente'; // Require admin approval
        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        $validated['numero_alunos'] = 0;
        $validated['rating'] = 0;

        $course = Course::create($validated);

        return response()->json($course, 201);
    }

    public function update(Request $request, $id)
    {
        $course = Course::where('id', $id)->where('trainer_id', $request->user()->id)->firstOrFail();

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
            'preco_normal' => 'sometimes|required|numeric|min:0',
            'description' => 'nullable|string',
            'modalidade' => 'sometimes|required|in:presencial,semi-presencial,ao-vivo,online',
        ]);

        $course->update($validated);

        return response()->json($course);
    }

    public function destroy(Request $request, $id)
    {
        $course = Course::where('id', $id)->where('trainer_id', $request->user()->id)->firstOrFail();
        
        // Cannot delete published courses without admin intervention usually, but let's allow if no enrollments
        if ($course->enrollments()->count() > 0) {
            return response()->json(['message' => 'Cannot delete a course that has students enrolled.'], 403);
        }

        $course->delete();

        return response()->json(['message' => 'Course deleted successfully.']);
    }
}
