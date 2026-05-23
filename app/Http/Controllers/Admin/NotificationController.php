<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        // Mocking the notifications for the admin panel
        $notifications = [
            [
                'id' => 1,
                'title' => "Novo certificado emitido",
                'body' => "Parabéns! O teu certificado React Professional foi emitido com sucesso.",
                'audience' => "Aluno",
                'type' => "sucesso",
                'sent' => 234,
                'read' => 198,
                'date' => "Hoje, 10:00",
                'status' => "enviado"
            ],
            [
                'id' => 2,
                'title' => "Novo curso disponível",
                'body' => "O curso 'Docker & Kubernetes na Prática' está agora disponível.",
                'audience' => "Todos",
                'type' => "info",
                'sent' => 3847,
                'read' => 1923,
                'date' => "Ontem, 09:00",
                'status' => "enviado"
            ],
            [
                'id' => 3,
                'title' => "Manutenção programada",
                'body' => "A plataforma estará em manutenção no dia 5 de Abril das 02:00 às 04:00.",
                'audience' => "Todos",
                'type' => "aviso",
                'sent' => 3847,
                'read' => 3102,
                'date' => "22 Mar, 08:00",
                'status' => "enviado"
            ],
            [
                'id' => 4,
                'title' => "Sessão de mentoria confirmada",
                'body' => "A tua sessão de mentoria para amanhã às 15:00 foi confirmada.",
                'audience' => "Aluno",
                'type' => "info",
                'sent' => 0,
                'read' => 0,
                'date' => "Amanhã, 08:00",
                'status' => "agendado"
            ],
            [
                'id' => 5,
                'title' => "Boas-vindas à plataforma",
                'body' => "Bem-vindo(a) ao AcademTI! Começa a tua jornada de aprendizagem hoje.",
                'audience' => "Aluno",
                'type' => "sucesso",
                'sent' => 0,
                'read' => 0,
                'date' => "Automático no registo",
                'status' => "automático"
            ]
        ];

        return response()->json($notifications);
    }
}
