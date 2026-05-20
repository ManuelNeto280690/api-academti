<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('company')
            ->with('companyProfile');

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

        $companies = $query->latest()->paginate(10);
        
        return response()->json($companies);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string',
            'status' => 'required|string|in:ativo,inativo,suspenso',
            'company_name' => 'required|string|max:255',
            'nif' => 'required|string|max:20|unique:company_profiles,nif',
            'sector' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'employees_count' => 'nullable|integer|min:0',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'status' => $validated['status'],
        ]);

        $user->assignRole('company');

        // Criar Perfil Corporativo
        $user->companyProfile()->create([
            'company_name' => $validated['company_name'],
            'nif' => $validated['nif'],
            'sector' => $validated['sector'],
            'address' => $validated['address'],
            'employees_count' => $validated['employees_count'] ?? 0,
        ]);

        // Enviar Email de Boas-vindas
        try {
            Mail::to($user->email)->send(new WelcomeMail($user, $validated['password']));
        } catch (\Exception $e) {
            \Log::error("Erro ao enviar email para empresa: " . $e->getMessage());
        }

        return response()->json([
            'message' => 'Empresa registada com sucesso',
            'company' => $user->load('companyProfile'),
        ], 201);
    }

    public function show(User $company)
    {
        if (!$company->hasRole('company')) {
            return response()->json(['message' => 'Utilizador não é uma empresa'], 403);
        }

        return response()->json($company->load(['companyProfile.students', 'roles']));
    }

    public function update(Request $request, User $company)
    {
        if (!$company->hasRole('company')) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $company->id,
            'phone' => 'nullable|string',
            'status' => 'sometimes|string|in:ativo,inativo,suspenso',
            'company_name' => 'sometimes|string|max:255',
            'nif' => 'sometimes|string|max:20|unique:company_profiles,nif,' . ($company->companyProfile->id ?? 'null'),
            'sector' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'employees_count' => 'nullable|integer|min:0',
        ]);

        $company->update($request->only(['name', 'email', 'phone', 'status']));

        // Atualizar perfil corporativo
        $company->companyProfile()->updateOrCreate(
            ['user_id' => $company->id],
            $request->only(['company_name', 'nif', 'sector', 'address', 'employees_count'])
        );

        return response()->json([
            'message' => 'Dados da empresa atualizados',
            'company' => $company->load('companyProfile'),
        ]);
    }

    public function destroy(User $company)
    {
        if (!$company->hasRole('company')) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $company->delete();
        return response()->json(['message' => 'Empresa removida com sucesso']);
    }
}
