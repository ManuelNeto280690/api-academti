<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'course_id' => 'required|uuid|exists:courses,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        // Check if student is enrolled (active/completed)
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $validated['course_id'])
            ->whereIn('status', ['ativo', 'concluido'])
            ->firstOrFail();

        if ($validated['comment'] && strip_tags($validated['comment']) !== $validated['comment']) {
            return response()->json([
                'message' => 'Uso de tags HTML não é permitido nos comentários.'
            ], 422);
        }

        $review = CourseReview::updateOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $validated['course_id'],
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ? trim($validated['comment']) : null,
            ]
        );

        // Update course average rating cache if it exists (in the plan we keep it simple)
        $course = Course::find($validated['course_id']);
        $avg = CourseReview::where('course_id', $course->id)->avg('rating');
        $course->update(['rating' => round($avg, 1)]);

        return response()->json([
            'message' => 'Avaliação enviada com sucesso!',
            'review' => $review,
            'new_rating' => $course->rating
        ]);
    }
}
