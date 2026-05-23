<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Certification;
use App\Models\Enrollment;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        // Totais Gerais
        $totalStudents = User::role('student')->count();
        $activeCourses = Course::where('status', 'ativo')->count();
        // In this system, issued certificates can be tracked by completed enrollments or a Certificates model if exists.
        // Let's assume completed enrollments represent issued certificates for now.
        $issuedCertificates = Enrollment::where('progress', 100)->count();
        $totalRevenue = Transaction::where('status', 'concluído')->where('type', '!=', 'Pagamento de Comissão')->sum('amount');

        // Para os crescimentos, precisamos contar o mês passado vs este mês
        $startOfMonth = now()->startOfMonth();
        $startOfLastMonth = now()->subMonth()->startOfMonth();
        $endOfLastMonth = now()->subMonth()->endOfMonth();

        $studentsThisMonth = User::role('student')->where('created_at', '>=', $startOfMonth)->count();
        $studentsLastMonth = User::role('student')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        
        $revenueThisMonth = Transaction::where('status', 'concluído')->where('type', '!=', 'Pagamento de Comissão')->where('created_at', '>=', $startOfMonth)->sum('amount');
        $revenueLastMonth = Transaction::where('status', 'concluído')->where('type', '!=', 'Pagamento de Comissão')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('amount');

        // Recent users
        $recentUsers = User::latest()->take(5)->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()->name ?? 'Utilizador',
                'date' => $user->created_at->format('Y-m-d H:i')
            ];
        });

        // Recent Enrollments (Activity)
        $recentEnrollments = Enrollment::with(['user', 'course', 'certification'])->latest()->take(5)->get()->map(function ($enr) {
            $itemName = $enr->course ? $enr->course->title : ($enr->certification ? $enr->certification->title : 'Mentoria');
            return [
                'id' => $enr->id,
                'user' => $enr->user->name ?? 'Desconhecido',
                'action' => 'Inscreveu-se em',
                'target' => $itemName,
                'time' => $enr->created_at->diffForHumans()
            ];
        });

        // Gráfico mock (já que Dashboard n precisa do anual completo, ou podemos fornecer)
        $chartData = [
            'labels' => ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
            'views' => [120, 150, 180, 130, 210, 250, 200],
            'enrollments' => [12, 15, 18, 13, 21, 25, 20]
        ];

        $topCourses = Course::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->take(4)
            ->get()
            ->map(function($course) {
                return [
                    'title' => $course->title,
                    'students' => $course->enrollments_count,
                    'rating' => 4.8,
                    'revenue' => $course->price * $course->enrollments_count
                ];
            });

        return response()->json([
            'stats' => [
                'totalStudents' => $totalStudents,
                'studentsGrowth' => $this->calculateGrowth($studentsThisMonth, $studentsLastMonth),
                'activeCourses' => $activeCourses,
                'coursesGrowth' => 0, // static or could be calculated
                'issuedCertificates' => $issuedCertificates,
                'certificatesGrowth' => 0,
                'totalRevenue' => $totalRevenue,
                'revenueGrowth' => $this->calculateGrowth($revenueThisMonth, $revenueLastMonth)
            ],
            'recentUsers' => $recentUsers,
            'recentActivity' => $recentEnrollments,
            'topCourses' => $topCourses,
            'chartData' => $chartData
        ]);
    }

    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 1);
    }
}
