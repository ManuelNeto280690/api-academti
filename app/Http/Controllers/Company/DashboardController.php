<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'stats' => [
                ['label' => "Funcionários Registados", 'value' => "15", 'change' => "+3 este mês", 'color' => "hsl(195 100% 50%)", 'bg' => "hsl(195 100% 50% / 0.1)"],
                ['label' => "Vagas Activas", 'value' => "2", 'change' => "Nenhuma a expirar", 'color' => "hsl(160 100% 50%)", 'bg' => "hsl(160 100% 50% / 0.1)"],
                ['label' => "Candidaturas Recebidas", 'value' => "45", 'change' => "+12 esta semana", 'color' => "hsl(271 81% 70%)", 'bg' => "hsl(271 81% 56% / 0.15)"],
                ['label' => "Formações em Curso", 'value' => "4", 'change' => "28 funcionários a frequentar", 'color' => "hsl(38 92% 60%)", 'bg' => "hsl(38 92% 50% / 0.1)"],
            ],
            'activeJobs' => [
                ['title' => "Engenheiro de Software", 'location' => "Luanda (Híbrido)", 'applications' => 12, 'days' => 15, 'status' => "activa"],
                ['title' => "Analista de Dados", 'location' => "Remoto", 'applications' => 33, 'days' => 5, 'status' => "activa"],
            ],
            'recentApplications' => [
                ['name' => "João Ferreira", 'job' => "Engenheiro de Software", 'date' => "há 1h", 'status' => "novo"],
                ['name' => "Catarina Silva", 'job' => "Analista de Dados", 'date' => "há 3h", 'status' => "análise"],
                ['name' => "Bruno Martins", 'job' => "Analista de Dados", 'date' => "há 5h", 'status' => "entrevista"],
                ['name' => "Mariana Costa", 'job' => "Engenheiro de Software", 'date' => "Ontem", 'status' => "novo"],
            ],
            'topCandidates' => [
                ['name' => "Ana Costa", 'title' => "React Developer", 'skills' => ["React", "TypeScript", "Node.js"], 'rating' => 4.9, 'available' => true],
                ['name' => "Rui Pereira", 'title' => "DevOps Engineer", 'skills' => ["Docker", "AWS", "Kubernetes"], 'rating' => 4.8, 'available' => true],
            ]
        ]);
    }
}
