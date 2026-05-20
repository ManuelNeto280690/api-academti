<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MentorProfile;
use Illuminate\Support\Facades\Hash;

class MentorSeeder extends Seeder
{
    public function run(): void
    {
        $mentors = [
            [
                'name' => 'Dr. Fernandes Lima',
                'email' => 'fernandes@ceftic.ao',
                'expertise' => ['Cibersegurança', 'Redes Cisco', 'Gestão de Crise'],
                'bio' => 'Especialista em segurança ofensiva com mais de 15 anos no sector bancário.',
                'price' => 25000.00
            ],
            [
                'name' => 'Maria Júlia',
                'email' => 'julia@ceftic.ao',
                'expertise' => ['UI/UX Design', 'Figma', 'Product Discovery'],
                'bio' => 'Apaixonada por criar interfaces que resolvem problemas reais de utilizadores.',
                'price' => 15000.00
            ],
            [
                'name' => 'António Pedro',
                'email' => 'antonio@ceftic.ao',
                'expertise' => ['Python para Dados', 'Inteligência Artificial', 'PowerBI'],
                'bio' => 'Ajudando empresas a tomar decisões baseadas em dados.',
                'price' => 20000.00
            ],
            [
                'name' => 'Cláudia Bento',
                'email' => 'claudia@ceftic.ao',
                'expertise' => ['Soft Skills', 'Liderança de Equipas', 'Public Speaking'],
                'bio' => 'Mentoria focada no desenvolvimento humano e competências comportamentais.',
                'price' => 12000.00
            ],
            [
                'name' => 'Ricardo Silva',
                'email' => 'ricardo@ceftic.ao',
                'expertise' => ['Desenvolvimento Laravel', 'DevOps', 'AWS Cloud'],
                'bio' => 'Consultor senior focado em escalabilidade de sistemas web.',
                'price' => 30000.00
            ],
        ];

        foreach ($mentors as $m) {
            $user = User::updateOrCreate(
                ['email' => $m['email']],
                [
                    'name' => $m['name'],
                    'password' => 'elite@2025',
                    'role' => 'mentor',
                    'status' => 'ativo',
                    'phone' => '+244 923 ' . rand(100, 999) . ' ' . rand(100, 999),
                ]
            );

            if (!$user->hasRole('mentor')) {
                $user->assignRole('mentor');
            }

            // Criar ou atualizar perfil
            $user->mentorProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'expertise' => $m['expertise'],
                    'bio' => $m['bio'],
                    'price_per_session' => $m['price'],
                    'rating' => rand(4, 5)
                ]
            );
        }
    }
}
