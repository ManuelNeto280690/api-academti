<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        // Mocking courses contracted by the company
        $trainings = [
            [
                'id' => 1,
                'course' => "React Avançado & Arquitectura",
                'category' => "Desenvolvimento",
                'enrolled' => 5,
                'progress' => 75,
                'nextSession' => "20 Mar - 14:00",
            ],
            [
                'id' => 2,
                'course' => "Cibersegurança para Empresas",
                'category' => "Segurança",
                'enrolled' => 12,
                'progress' => 40,
                'nextSession' => "Exame em 2 dias",
            ],
            [
                'id' => 3,
                'course' => "Node.js & Microservices",
                'category' => "Backend",
                'enrolled' => 3,
                'progress' => 90,
                'nextSession' => "Finalizado",
            ]
        ];

        return response()->json($trainings);
    }

    public function enrollments(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|integer',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'integer'
        ]);

        // Here we would attach the employees to the course enrollments in DB
        // For now, we return success response
        
        return response()->json([
            'message' => 'Inscrição efetuada com sucesso.',
            'enrolled_count' => count($validated['employee_ids'])
        ], 200);
    }
}
