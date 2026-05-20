<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

class MentorController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('mentor')
            ->with('mentorProfile');

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

        $mentors = $query->latest()->paginate(10);
        
        return response()->json($mentors);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string',
            'status' => 'required|string|in:ativo,inativo,suspenso',
            'expertise' => 'nullable|array',
            'bio' => 'nullable|string',
            'price_per_session' => 'nullable|numeric|min:0',
        ]);

        // Verificar se o utilizador já existe (caso queira tornar um admin/formador em mentor)
        $user = User::where('email', $validated['email'])->first();

        if ($user) {
            $user->assignRole('mentor');
        } else {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'status' => $validated['status'],
            ]);
            $user->assignRole('mentor');

            // Enviar Email apenas para novos utilizadores
            try {
                Mail::to($user->email)->send(new WelcomeMail($user, $validated['password']));
            } catch (\Exception $e) {
                \Log::error("Erro ao enviar email para mentor: " . $e->getMessage());
            }
        }

        // Criar ou atualizar perfil de mentoria
        $user->mentorProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'expertise' => $validated['expertise'] ?? [],
                'bio' => $validated['bio'] ?? null,
                'price_per_session' => $validated['price_per_session'] ?? 0,
            ]
        );

        return response()->json([
            'message' => 'Mentor configurado com sucesso',
            'mentor' => $user->load('mentorProfile'),
        ], 201);
    }

    public function show(User $mentor)
    {
        if (!$mentor->hasRole('mentor')) {
            return response()->json(['message' => 'Utilizador não é um mentor'], 403);
        }

        return response()->json($mentor->load(['mentorProfile', 'roles']));
    }

    public function update(Request $request, User $mentor)
    {
        if (!$mentor->hasRole('mentor')) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $mentor->id,
            'phone' => 'nullable|string',
            'status' => 'sometimes|string|in:ativo,inativo,suspenso',
            'expertise' => 'nullable|array',
            'bio' => 'nullable|string',
            'price_per_session' => 'nullable|numeric|min:0',
        ]);

        $mentor->update($request->only(['name', 'email', 'phone', 'status']));

        // Atualizar perfil de mentoria
        $mentor->mentorProfile()->updateOrCreate(
            ['user_id' => $mentor->id],
            $request->only(['expertise', 'bio', 'price_per_session'])
        );

        return response()->json([
            'message' => 'Dados do mentor atualizados',
            'mentor' => $mentor->load('mentorProfile'),
        ]);
    }

    public function destroy(User $mentor)
    {
        if (!$mentor->hasRole('mentor')) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        // Apenas removemos o papel de mentor e o perfil, mas não o utilizador (pois pode ser admin/trainer)
        // Se o utilizador SÓ tiver o papel de mentor, poderíamos eliminá-lo, mas por segurança removemos apenas o acesso.
        
        $mentor->removeRole('mentor');
        $mentor->mentorProfile()->delete();

        return response()->json(['message' => 'Acesso de mentoria removido']);
    }
}
