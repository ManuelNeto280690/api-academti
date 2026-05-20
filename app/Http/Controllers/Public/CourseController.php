<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        return Course::with(['category', 'trainer', 'materials'])
            ->where('status', 'publicado')
            ->latest()
            ->get();
    }

    public function show(Course $course)
    {
        if ($course->status !== 'publicado') {
            return response()->json(['message' => 'Não encontrado.'], 404);
        }

        // Load modules with lessons and quizzes
        $course->load([
            'category', 
            'trainer', 
            'modules.lessons' => function($q) { $q->orderBy('order'); },
            'modules.quizzes',
            'quizzes',
            'requirements',
            'learningOutcomes',
            'reviews.user'
        ]);

        // Filter sensitive data from lessons for public view, except for previews
        $course->modules->each(function($module) {
            $module->lessons->each(function($lesson) {
                if (!$lesson->is_preview) {
                    $lesson->makeHidden(['video_url', 'meeting_link', 'content']);
                }
            });
        });

        // Check if current user is enrolled
        $user = auth('sanctum')->user();
        $isEnrolled = false;
        $enrollmentStatus = null;

        if ($user) {
            $enrollment = $user->enrollments()->where('course_id', $course->id)->first();
            if ($enrollment) {
                $isEnrolled = true;
                $enrollmentStatus = $enrollment->status;
            }
        }

        $course->is_enrolled = $isEnrolled;
        $course->enrollment_status = $enrollmentStatus;

        return response()->json($course);
    }
}
