<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Enrollment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Ensure user is formador (we will have a middleware for this, but let's be safe)
        $courses = Course::where('trainer_id', $user->id)->get();

        $publishedCourses = $courses->where('status', 'publicado')->count();
        $totalStudents = $courses->sum('numero_alunos');
        
        // Average rating
        $avgRating = $courses->avg('rating') ?: 0;

        // Revenue (estimate based on enrolled students * price)
        // Note: For a real platform, you would query an `invoices` or `payments` table.
        $revenue = $courses->sum(function($course) {
            $price = $course->preco_promocional ?: $course->preco_normal;
            return $course->numero_alunos * $price;
        });

        // My Courses for the list
        $myCourses = $courses->map(function($course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
                'students' => $course->numero_alunos,
                'rating' => $course->rating,
                'progress' => 100, // Or calculate based on lessons created vs expected
                'status' => $course->status,
                'reviews' => $course->reviews()->count(),
            ];
        })->values();

        return response()->json([
            'stats' => [
                'published_courses' => $publishedCourses,
                'total_students' => $totalStudents,
                'average_rating' => round($avgRating, 1),
                'revenue' => round($revenue, 2)
            ],
            'chart_data' => [
                ['name' => 'Seg', 'value' => 120],
                ['name' => 'Ter', 'value' => 150],
                ['name' => 'Qua', 'value' => 100],
                ['name' => 'Qui', 'value' => 200],
                ['name' => 'Sex', 'value' => 180],
                ['name' => 'Sáb', 'value' => 250],
                ['name' => 'Dom', 'value' => 300],
            ],
            'my_courses' => $myCourses,
            'upcoming_mentorias' => [], // To be implemented with Mentorships
            'pending_reviews' => [],    // To be implemented with Assignments
        ]);
    }
}
