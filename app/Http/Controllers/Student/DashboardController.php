<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Total enrolled courses
        $totalEnrolled = Enrollment::where('user_id', $user->id)->count();
        
        // Completed courses (progress 100%)
        $completedCourses = Enrollment::where('user_id', $user->id)
            ->where('progress', '>=', 100)
            ->count();
            
        // Active courses (specifically those with progress > 0 and < 100)
        $activeCoursesCount = Enrollment::where('user_id', $user->id)
            ->where('progress', '>', 0)
            ->where('progress', '<', 100)
            ->count();
            
        // Current enrolled courses with detailed progress for the dashboard list
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with(['course.trainer', 'certification.category'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($enrollment) {
                if ($enrollment->course) {
                    return [
                        'id' => $enrollment->course->id,
                        'title' => $enrollment->course->title,
                        'progress' => $enrollment->progress,
                        'status' => $enrollment->status,
                        'modalidade' => $enrollment->course->modalidade,
                        'trainer' => $enrollment->course->trainer ? $enrollment->course->trainer->name : 'N/A',
                        'image' => $enrollment->course->imagem,
                        'is_certification' => false,
                    ];
                } elseif ($enrollment->certification) {
                    return [
                        'id' => $enrollment->certification->id,
                        'title' => $enrollment->certification->title,
                        'progress' => $enrollment->progress,
                        'status' => $enrollment->status,
                        'modalidade' => $enrollment->certification->type,
                        'trainer' => 'CEFTIC Official',
                        'image' => null, // Certifications don't have images yet
                        'is_certification' => true,
                    ];
                }
                return null;
            })->filter();

        return response()->json([
            'stats' => [
                'total_courses' => $totalEnrolled,
                'completed_courses' => $completedCourses,
                'active_courses' => $activeCoursesCount,
                'certificates' => $completedCourses, // Mock: For now, certificates = completed courses
                'hours' => 0, // Mock: Hours need a log system
                'points' => 0, // Mock: Points need a gamification system
            ],
            'active_enrollments' => $enrollments,
        ]);
    }
}
