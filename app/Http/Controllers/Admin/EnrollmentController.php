<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::with([
            'user:id,name,email,phone', 
            'course:id,title,modalidade,preco_normal,category_id', 
            'course.category:id,name',
            'certification:id,title,type,price,category_id',
            'certification.category:id,name'
        ]);

        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->has('certification_id')) {
            $query->where('certification_id', $request->certification_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }

        return response()->json($query->latest()->paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'status' => 'required|string|in:ativo,concluido,cancelado,pendente',
        ]);

        // Check if already enrolled
        $exists = Enrollment::where('user_id', $validated['user_id'])
            ->where('course_id', $validated['course_id'])
            ->first();

        if ($exists) {
            return response()->json(['message' => 'Este utilizador já está inscrito neste curso.'], 422);
        }

        $enrollment = Enrollment::create($validated);

        return response()->json([
            'message' => 'Inscrição realizada com sucesso.',
            'enrollment' => $enrollment->load(['user', 'course'])
        ], 201);
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:ativo,concluido,cancelado,pendente',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        $enrollment->update($validated);

        return response()->json([
            'message' => 'Inscrição atualizada com sucesso.',
            'enrollment' => $enrollment->load(['user', 'course'])
        ]);
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();
        return response()->json(['message' => 'Inscrição removida com sucesso.']);
    }
}
