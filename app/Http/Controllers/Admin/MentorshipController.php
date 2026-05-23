<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MentorshipController extends Controller
{
    public function index()
    {
        // Mocking the mentorship sessions for the admin panel
        $sessions = [
            [
                'id' => 1,
                'studentName' => "Maria Santos",
                'studentAvatar' => "MS",
                'mentorName' => "Dr. Paulo Mendes",
                'mentorAvatar' => "PM",
                'mentorSpecialty' => "Full Stack",
                'topic' => "Arquitectura de Microservices",
                'date' => "28 Mar 2026",
                'time' => "18:00",
                'duration' => "60 min",
                'status' => "em_curso",
                'price' => "€ 85"
            ],
            [
                'id' => 2,
                'studentName' => "João Ferreira",
                'studentAvatar' => "JF",
                'mentorName' => "Dra. Catarina Lima",
                'mentorAvatar' => "CL",
                'mentorSpecialty' => "Data Science",
                'topic' => "Modelos de ML com Python e Scikit-learn",
                'date' => "28 Mar 2026",
                'time' => "15:00",
                'duration' => "90 min",
                'status' => "concluída",
                'price' => "€ 142",
                'rating' => 5,
                'feedback' => "Excelente sessão! A Dra. Catarina explicou muito bem todos os conceitos."
            ],
            [
                'id' => 3,
                'studentName' => "Ana Costa",
                'studentAvatar' => "AC",
                'mentorName' => "Eng. Roberto Silva",
                'mentorAvatar' => "RS",
                'mentorSpecialty' => "Cloud & DevOps",
                'topic' => "CI/CD com GitHub Actions e Docker",
                'date' => "29 Mar 2026",
                'time' => "10:00",
                'duration' => "60 min",
                'status' => "agendada",
                'price' => "€ 75"
            ],
            [
                'id' => 4,
                'studentName' => "Pedro Alves",
                'studentAvatar' => "PA",
                'mentorName' => "Dr. Paulo Mendes",
                'mentorAvatar' => "PM",
                'mentorSpecialty' => "Full Stack",
                'topic' => "Code Review e boas práticas TypeScript",
                'date' => "29 Mar 2026",
                'time' => "19:00",
                'duration' => "45 min",
                'status' => "agendada",
                'price' => "€ 63"
            ],
            [
                'id' => 5,
                'studentName' => "Carlos Fonseca",
                'studentAvatar' => "CF",
                'mentorName' => "Ana Beatriz Costa",
                'mentorAvatar' => "AB",
                'mentorSpecialty' => "Product Management",
                'topic' => "Como criar e priorizar um produto backlog",
                'date' => "27 Mar 2026",
                'time' => "14:00",
                'duration' => "60 min",
                'status' => "concluída",
                'price' => "€ 80",
                'rating' => 4,
                'feedback' => "Muito útil para o meu trabalho actual."
            ],
            [
                'id' => 6,
                'studentName' => "Inês Rodrigues",
                'studentAvatar' => "IR",
                'mentorName' => "Dra. Catarina Lima",
                'mentorAvatar' => "CL",
                'mentorSpecialty' => "Data Science",
                'topic' => "Deep Learning com TensorFlow",
                'date' => "30 Mar 2026",
                'time' => "11:00",
                'duration' => "120 min",
                'status' => "pendente",
                'price' => "€ 190"
            ],
            [
                'id' => 7,
                'studentName' => "Rui Martins",
                'studentAvatar' => "RM",
                'mentorName' => "Eng. Roberto Silva",
                'mentorAvatar' => "RS",
                'mentorSpecialty' => "Cloud & DevOps",
                'topic' => "Kubernetes: Deployments e Scaling",
                'date' => "26 Mar 2026",
                'time' => "16:00",
                'duration' => "60 min",
                'status' => "cancelada",
                'price' => "€ 75"
            ],
            [
                'id' => 8,
                'studentName' => "Sofia Lopes",
                'studentAvatar' => "SL",
                'mentorName' => "Dr. Paulo Mendes",
                'mentorAvatar' => "PM",
                'mentorSpecialty' => "Full Stack",
                'topic' => "Introdução ao Go para backends",
                'date' => "31 Mar 2026",
                'time' => "20:00",
                'duration' => "60 min",
                'status' => "agendada",
                'price' => "€ 85"
            ],
        ];

        return response()->json($sessions);
    }
}
