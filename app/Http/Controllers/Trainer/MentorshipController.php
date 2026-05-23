<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event; // Or whatever mentorship model we decide on later

class MentorshipController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Em breve será feita a integração com a tabela de marcações de mentorias
        // Por agora retornamos vazio para ligar o Frontend.
        $mentorias = [];

        return response()->json($mentorias);
    }

    public function stats(Request $request)
    {
        // Estatísticas para o Dashboard e página de Estatísticas do Formador
        // Simulação de dados dinâmicos por enquanto.

        return response()->json([
            'total_students' => 1247,
            'active_courses' => 8,
            'revenue_month' => 3120,
            'completion_rate' => 82,
            'chart_revenue' => [
                ['name' => 'Jan', 'value' => 1200],
                ['name' => 'Fev', 'value' => 2100],
                ['name' => 'Mar', 'value' => 3120],
                ['name' => 'Abr', 'value' => 0],
            ]
        ]);
    }
}
