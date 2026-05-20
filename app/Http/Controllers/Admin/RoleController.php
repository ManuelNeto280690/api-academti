<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return response()->json(Role::with('permissions')->get());
    }

    public function permissions()
    {
        return response()->json(Permission::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'message' => 'Função criada com sucesso',
            'role' => $role->load('permissions'),
        ], 201);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'sometimes|string|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
        ]);

        if ($request->has('name')) {
            $role->name = $request->name;
            $role->save();
        }

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'message' => 'Função atualizada com sucesso',
            'role' => $role->load('permissions'),
        ]);
    }

    public function destroy(Role $role)
    {
        // Impede a remoção do admin root
        if ($role->name === 'admin') {
            return response()->json(['message' => 'A função de administrador não pode ser removida'], 403);
        }

        $role->delete();
        return response()->json(['message' => 'Função removida com sucesso']);
    }
}
