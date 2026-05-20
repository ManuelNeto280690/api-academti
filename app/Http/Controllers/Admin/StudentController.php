<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('student')
            ->withCount('enrollments')
            ->with(['enrollments.course', 'company']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== 'todos') {
            $query->where('status', $request->status);
        }

        $students = $query->latest()->paginate(10);
        
        return response()->json($students);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string',
            'status' => 'required|string|in:ativo,inativo,suspenso',
            'company_profile_id' => 'nullable|uuid|exists:company_profiles,id',
        ]);

        $student = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role' => 'student',
            'status' => $validated['status'],
            'company_profile_id' => $validated['company_profile_id'] ?? null,
        ]);

        $student->assignRole('student');

        // Enviar Email de Boas-vindas
        try {
            Mail::to($student->email)->send(new WelcomeMail($student, $validated['password']));
        } catch (\Exception $e) {
            \Log::error("Erro ao enviar email para aluno: " . $e->getMessage());
        }

        return response()->json([
            'message' => 'Aluno criado com sucesso',
            'student' => $student->loadCount('enrollments')->load('company'),
        ], 201);
    }

    public function show(User $student)
    {
        // Verificar se é realmente um aluno
        if (!$student->hasRole('student')) {
            return response()->json(['message' => 'Utilizador não é um aluno'], 403);
        }

        return response()->json($student->load(['enrollments.course', 'roles', 'company']));
    }

    public function update(Request $request, User $student)
    {
        if (!$student->hasRole('student')) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $student->id,
            'phone' => 'nullable|string',
            'status' => 'sometimes|string|in:ativo,inativo,suspenso',
            'company_profile_id' => 'nullable|uuid|exists:company_profiles,id',
        ]);

        $student->update($validated);

        return response()->json([
            'message' => 'Dados do aluno atualizados',
            'student' => $student->loadCount('enrollments')->load('company'),
        ]);
    }

    public function destroy(User $student)
    {
        if (!$student->hasRole('student')) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $student->delete();
        return response()->json(['message' => 'Aluno removido com sucesso']);
    }
}
