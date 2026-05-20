<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

class TrainerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('trainer')
            ->withCount('taughtCourses')
            ->with('profile');

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

        $trainers = $query->latest()->paginate(10);
        
        return response()->json($trainers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string',
            'status' => 'required|string|in:ativo,inativo,suspenso',
            'specialty' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0',
            'bio' => 'nullable|string',
        ]);

        $trainer = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role' => 'trainer',
            'status' => $validated['status'],
        ]);

        $trainer->assignRole('trainer');

        // Criar Perfil de Formador
        $trainer->profile()->create([
            'specialty' => $validated['specialty'] ?? null,
            'experience_years' => $validated['experience_years'] ?? 0,
            'bio' => $validated['bio'] ?? null,
        ]);

        // Enviar Email de Boas-vindas (credenciais)
        try {
            Mail::to($trainer->email)->send(new WelcomeMail($trainer, $validated['password']));
        } catch (\Exception $e) {
            \Log::error("Erro ao enviar email para formador: " . $e->getMessage());
        }

        return response()->json([
            'message' => 'Formador criado com sucesso',
            'trainer' => $trainer->loadCount('taughtCourses')->load('profile'),
        ], 201);
    }

    public function show(User $trainer)
    {
        if (!$trainer->hasRole('trainer')) {
            return response()->json(['message' => 'Utilizador não é um formador'], 403);
        }

        return response()->json($trainer->load(['taughtCourses.category', 'roles', 'profile']));
    }

    public function update(Request $request, User $trainer)
    {
        if (!$trainer->hasRole('trainer')) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $trainer->id,
            'phone' => 'nullable|string',
            'status' => 'sometimes|string|in:ativo,inativo,suspenso',
            'specialty' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0',
            'bio' => 'nullable|string',
        ]);

        $trainer->update($request->only(['name', 'email', 'phone', 'status']));

        // Atualizar ou Criar Perfil
        $trainer->profile()->updateOrCreate(
            ['user_id' => $trainer->id],
            $request->only(['specialty', 'experience_years', 'bio'])
        );

        return response()->json([
            'message' => 'Dados do formador atualizados',
            'trainer' => $trainer->loadCount('taughtCourses')->load('profile'),
        ]);
    }

    public function destroy(User $trainer)
    {
        if (!$trainer->hasRole('trainer')) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        // Antes de eliminar, poderíamos verificar se ele tem cursos activos e avisar
        $trainer->delete();
        return response()->json(['message' => 'Formador removido com sucesso']);
    }
}
