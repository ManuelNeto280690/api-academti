<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Limpar cache de permissões
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── 1. CRIAR PERMISSÕES ───

        $permissions = [
            // Utilizadores
            'ver-utilizadores', 'criar-utilizadores', 'editar-utilizadores', 'eliminar-utilizadores', 'suspender-utilizadores',
            // Alunos
            'ver-alunos', 'criar-alunos', 'editar-alunos', 'eliminar-alunos',
            // Formadores
            'ver-formadores', 'criar-formadores', 'editar-formadores', 'eliminar-formadores',
            // Mentores
            'ver-mentores', 'criar-mentores', 'editar-mentores', 'eliminar-mentores',
            // Empresas
            'ver-empresas', 'criar-empresas', 'editar-empresas', 'eliminar-empresas',
            // Categorias
            'ver-categorias', 'criar-categorias', 'editar-categorias', 'eliminar-categorias',
            // Roles & Permissions
            'gerir-acessos',
            // Cursos
            'ver-cursos', 'aprovar-cursos', 'editar-cursos', 'eliminar-cursos', 'criar-cursos',
            // Mentorias
            'gerir-mentorias',
            // Financeiro & Relatórios
            'ver-relatorios', 'ver-faturacao',
            // Configurações
            'gestao-plataforma',
            // Certificações
            'ver-certificacoes', 'criar-certificacoes', 'editar-certificacoes', 'eliminar-certificacoes', 'emitir-certificados'
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission, 'guard_name' => 'api'],
                ['name' => $permission]
            );
        }

        // ─── 2. CRIAR PAPÉIS E ATRIBUIR PERMISSÕES ───

        // ADMIN: Tem tudo
        $adminRole = Role::updateOrCreate(
            ['name' => 'admin', 'guard_name' => 'api'],
            ['name' => 'admin']
        );
        $adminRole->syncPermissions(Permission::all());

        // FORMADOR (Trainer)
        $trainerRole = Role::updateOrCreate(
            ['name' => 'trainer', 'guard_name' => 'api'],
            ['name' => 'trainer']
        );
        $trainerRole->syncPermissions([
            'ver-cursos', 'criar-cursos', 'editar-cursos', 'gerir-mentorias'
        ]);

        // ALUNO (Student)
        $studentRole = Role::updateOrCreate(
            ['name' => 'student', 'guard_name' => 'api'],
            ['name' => 'student']
        );
        $studentRole->syncPermissions(['ver-cursos']);

        // EMPRESA (Company)
        $companyRole = Role::updateOrCreate(
            ['name' => 'company', 'guard_name' => 'api'],
            ['name' => 'company']
        );
        $companyRole->syncPermissions(['ver-cursos', 'ver-relatorios']);

        // MENTOR
        $mentorRole = Role::updateOrCreate(
            ['name' => 'mentor', 'guard_name' => 'api'],
            ['name' => 'mentor']
        );
        $mentorRole->syncPermissions(['gerir-mentorias']);

        // ─── 3. UTILIZADOR ROOT ───

        $rootAdmin = User::updateOrCreate(
            ['email' => 'admin@ceftic.ao'],
            [
                'name' => 'Ceftic Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'ativo'
            ]
        );

        $rootAdmin->assignRole($adminRole);
    }
}
