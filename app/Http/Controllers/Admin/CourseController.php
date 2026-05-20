<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['category', 'trainer', 'requirements', 'learningOutcomes']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->has('status') && $request->status !== 'todos') {
            $query->where('status', $request->status);
        }

        if ($request->has('modalidade') && $request->modalidade !== 'todos') {
            $query->where('modalidade', $request->modalidade);
        }

        $courses = $query->latest()->paginate(10);
        
        return response()->json($courses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'trainer_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'modalidade' => 'required|string|in:presencial,semi-presencial,ao-vivo,online',
            'location' => 'required_if:modalidade,presencial,semi-presencial|nullable|string',
            'meeting_link' => 'required_if:modalidade,ao-vivo,semi-presencial|nullable|url',
            'preco_normal' => 'required|numeric|min:0',
            'preco_promocional' => 'nullable|numeric|min:0',
            'imagem' => 'nullable|string', // URL or Path
            'status' => 'required|string|in:rascunho,pendente,publicado,rejeitado,arquivado',
            'sequential_unlock' => 'nullable|boolean',
            'min_pass_score' => 'nullable|integer|min:0|max:100',
            'access_duration_days' => 'nullable|integer|min:0',
            'certificate_enabled' => 'nullable|boolean',
            'show_learning_outcomes' => 'nullable|boolean',
            'show_requirements' => 'nullable|boolean',
            'learning_outcomes' => 'nullable|array',
            'requirements' => 'nullable|array',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']) . '-' . uniqid();

        $course = Course::create($validated);
        
        if ($request->has('learning_outcomes')) {
            $course->learningOutcomes()->delete();
            foreach ($request->learning_outcomes as $index => $item) {
                $course->learningOutcomes()->create(['description' => $item, 'order' => $index]);
            }
        }
        
        if ($request->has('requirements')) {
            $course->requirements()->delete();
            foreach ($request->requirements as $index => $item) {
                $course->requirements()->create(['description' => $item, 'order' => $index]);
            }
        }

        return response()->json([
            'message' => 'Curso criado com sucesso',
            'course' => $course->load(['category', 'trainer']),
        ], 201);
    }

    public function show(Course $course)
    {
        return response()->json($course->load([
            'category', 
            'trainer', 
            'modules.lessons', 
            'modules.quizzes', 
            'requirements', 
            'learningOutcomes', 
            'quizzes'
        ]));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'trainer_id' => 'sometimes|required|exists:users,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'modalidade' => 'sometimes|required|string|in:presencial,semi-presencial,ao-vivo,online',
            'location' => 'nullable|string',
            'meeting_link' => 'nullable|url',
            'preco_normal' => 'sometimes|required|numeric|min:0',
            'preco_promocional' => 'nullable|numeric|min:0',
            'imagem' => 'nullable|string',
            'status' => 'sometimes|required|string|in:rascunho,pendente,publicado,rejeitado,arquivado',
            'sequential_unlock' => 'nullable|boolean',
            'min_pass_score' => 'nullable|integer|min:0|max:100',
            'access_duration_days' => 'nullable|integer|min:0',
            'certificate_enabled' => 'nullable|boolean',
            'show_learning_outcomes' => 'nullable|boolean',
            'show_requirements' => 'nullable|boolean',
            'learning_outcomes' => 'nullable|array',
            'requirements' => 'nullable|array',
        ]);

        if (isset($validated['title']) && $validated['title'] !== $course->title) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']) . '-' . uniqid();
        }

        $course->update($validated);
        
        if ($request->has('learning_outcomes')) {
            $course->learningOutcomes()->delete();
            foreach ($request->learning_outcomes as $index => $item) {
                $course->learningOutcomes()->create(['description' => $item, 'order' => $index]);
            }
        }
        
        if ($request->has('requirements')) {
            $course->requirements()->delete();
            foreach ($request->requirements as $index => $item) {
                $course->requirements()->create(['description' => $item, 'order' => $index]);
            }
        }

        return response()->json([
            'message' => 'Curso atualizado com sucesso',
            'course' => $course->load(['category', 'trainer']),
        ]);
    }

    public function approve(Course $course)
    {
        $course->update(['status' => 'publicado']);
        return response()->json(['message' => 'Curso aprovado com sucesso!', 'course' => $course]);
    }

    public function reject(Request $request, Course $course)
    {
        $request->validate(['reason' => 'required|string']);
        $course->update(['status' => 'rejeitado']);
        return response()->json(['message' => 'Curso rejeitado.', 'course' => $course]);
    }

    public function destroy(Course $course)
    {
        if ($course->enrollments()->count() > 0) {
            return response()->json(['message' => 'Não é possível eliminar um curso que já possui alunos inscritos.'], 422);
        }

        $course->delete();
        return response()->json(['message' => 'Curso eliminado com sucesso.']);
    }
}
