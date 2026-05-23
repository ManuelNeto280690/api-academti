<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\Course;

class InteractionController extends Controller
{
    public function getReviews(Request $request)
    {
        $user = $request->user();

        // Get reviews for courses authored by this trainer
        // We assume CourseReview model has `course_id` and `user_id` (student)
        $reviews = CourseReview::whereHas('course', function($query) use ($user) {
            $query->where('trainer_id', $user->id);
        })
        ->with(['user', 'course'])
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json($reviews);
    }

    public function replyToReview(Request $request, $id)
    {
        $user = $request->user();

        $review = CourseReview::whereHas('course', function($query) use ($user) {
            $query->where('trainer_id', $user->id);
        })->where('id', $id)->firstOrFail();

        $validated = $request->validate([
            'reply' => 'required|string',
        ]);

        // Assuming there is a `reply` or `trainer_reply` field in CourseReview table
        // Or you might create a separate table for replies. For now, let's just mock it
        // by checking if the column exists or simulating the response if we don't have it.
        $review->trainer_reply = $validated['reply'];
        $review->save();

        return response()->json(['message' => 'Resposta enviada com sucesso.', 'review' => $review]);
    }

    public function getStudents(Request $request)
    {
        $user = $request->user();

        // Get enrollments for courses authored by this trainer
        $enrollments = Enrollment::whereHas('course', function($query) use ($user) {
            $query->where('trainer_id', $user->id);
        })
        ->with(['user', 'course'])
        ->orderBy('created_at', 'desc')
        ->get();

        // Group by user to show unique students, or keep as a list of enrollments
        $students = $enrollments->map(function ($enrollment) {
            return [
                'id' => $enrollment->user->id ?? '',
                'name' => $enrollment->user->name ?? 'Aluno Removido',
                'email' => $enrollment->user->email ?? '',
                'course' => $enrollment->course->title ?? '',
                'progress' => $enrollment->progress ?? 0,
                'enrolled_at' => $enrollment->created_at,
            ];
        });

        return response()->json($students);
    }

    public function getAssignments(Request $request)
    {
        // Currently, the assignment/submission model does not exist.
        // Returning an empty array to make the frontend dynamic.
        return response()->json([]);
    }
}
