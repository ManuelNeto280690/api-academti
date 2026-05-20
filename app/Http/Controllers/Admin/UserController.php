<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

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

        return response()->json($query->paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string',
            'role' => 'required|string|exists:roles,name',
            'status' => 'required|string|in:ativo,inativo,suspenso',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        $user->assignRole($validated['role']);

        // Enviar Email de Boas-vindas com a senha gerada
        try {
            Mail::to($user->email)->send(new WelcomeMail($user, $validated['password']));
        } catch (\Exception $e) {
            \Log::error("Erro ao enviar email de admin: " . $e->getMessage());
        }

        return response()->json([
            'message' => 'Utilizador criado com sucesso',
            'user' => $user->load('roles'),
        ], 201);
    }

    public function show(User $user)
    {
        return response()->json($user->load('roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string',
            'role' => 'sometimes|string|exists:roles,name',
            'status' => 'sometimes|string|in:ativo,inativo,suspenso',
        ]);

        $user->update($validated);

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return response()->json([
            'message' => 'Utilizador atualizado com sucesso',
            'user' => $user->load('roles'),
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Utilizador removido com sucesso']);
    }

    public function toggleStatus(User $user)
    {
        $user->status = ($user->status === 'ativo') ? 'suspenso' : 'ativo';
        $user->save();

        return response()->json([
            'message' => "Estado do utilizador alterado para {$user->status}",
            'status' => $user->status,
        ]);
    }
}
