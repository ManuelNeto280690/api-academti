<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TrainerSeeder extends Seeder
{
    public function run(): void
    {
        $trainers = [
            ['name' => 'Manuel Neto', 'email' => 'manuel@ceftic.ao'],
            ['name' => 'Ana Paula', 'email' => 'ana@ceftic.ao'],
            ['name' => 'Carlos Silva', 'email' => 'carlos@ceftic.ao'],
            ['name' => 'Sara Mendes', 'email' => 'sara@ceftic.ao'],
            ['name' => 'João Vala', 'email' => 'joao@ceftic.ao'],
        ];

        foreach ($trainers as $t) {
            $user = User::updateOrCreate(
                ['email' => $t['email']],
                [
                    'name' => $t['name'],
                    'password' => 'elite@2025',
                    'role' => 'trainer',
                    'phone' => '+244 912 ' . rand(100, 999) . ' ' . rand(100, 999),
                    'status' => 'ativo'
                ]
            );

            if (!$user->hasRole('trainer')) {
                $user->assignRole('trainer');
            }
        }
    }
}
