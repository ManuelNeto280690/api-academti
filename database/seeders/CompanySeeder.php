<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CompanyProfile;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'Unitel SA',
                'email' => 'rh@unitel.ao',
                'nif' => '5401123456',
                'sector' => 'Telecomunicações',
                'address' => 'Luanda, Talatona',
                'employees' => 3000
            ],
            [
                'name' => 'Sonangol EP',
                'email' => 'formacao@sonangol.co.ao',
                'nif' => '5401987654',
                'sector' => 'Petróleo e Gás',
                'address' => 'Luanda, Centro',
                'employees' => 8000
            ],
            [
                'name' => 'Banco BAI',
                'email' => 'capital.humano@bai.ao',
                'nif' => '5401556677',
                'sector' => 'Bancário',
                'address' => 'Luanda, Mutamba',
                'employees' => 2500
            ],
            [
                'name' => 'Zap Angola',
                'email' => 'vagas@zap.ao',
                'nif' => '5401223344',
                'sector' => 'Média e Entretenimento',
                'address' => 'Luanda, Belas',
                'employees' => 1200
            ],
            [
                'name' => 'ENDE EP',
                'email' => 'geral@ende.ao',
                'nif' => '5401445566',
                'sector' => 'Energia',
                'address' => 'Luanda, Kinaxixi',
                'employees' => 4500
            ],
        ];

        foreach ($companies as $c) {
            $user = User::updateOrCreate(
                ['email' => $c['email']],
                [
                    'name' => $c['name'],
                    'password' => 'elite@2025',
                    'role' => 'company',
                    'status' => 'ativo',
                    'phone' => '+244 923 ' . rand(100, 999) . ' ' . rand(100, 999),
                ]
            );

            if (!$user->hasRole('company')) {
                $user->assignRole('company');
            }

            // Criar ou atualizar perfil corporativo
            $user->companyProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $c['name'],
                    'nif' => $c['nif'],
                    'sector' => $c['sector'],
                    'address' => $c['address'],
                    'employees_count' => $c['employees'],
                ]
            );
        }
    }
}
