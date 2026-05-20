<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class QuizStudentController extends Controller
{
    public function index($enrollmentId)
    {
        $enrollment = Enrollment::with(['course.modules.quizzes', 'course.quizzes', 'user', 'certification'])->findOrFail($enrollmentId);
        $user = $enrollment->user;
        $course = $enrollment->course;

        if (!$course) {
            // For now, if no course (e.g. certification), return empty list or handle certification quizzes if they exist
            return response()->json([]);
        }

        // Get all quizzes related to the course
        $moduleQuizzes = $course->modules->flatMap->quizzes;
        $standaloneQuizzes = $course->quizzes;
        $allQuizzes = $moduleQuizzes->concat($standaloneQuizzes)->filter()->unique('id');

        $results = $allQuizzes->map(function($quiz) use ($user) {
            $performance = $user->completedQuizzes()->where('quiz_id', $quiz->id)->first();
            return [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'score' => $performance ? $performance->pivot->score : null,
                'status' => $performance ? $performance->pivot->status : 'pending',
                'is_locked' => $performance ? (bool)$performance->pivot->is_locked : false,
                'completed_at' => $performance ? $performance->pivot->completed_at : null,
            ];
        });

        return response()->json($results);
    }

    public function unlock(Request $request, $userId, $quizId)
    {
        $user = User::findOrFail($userId);
        $quiz = Quiz::findOrFail($quizId);

        $user->completedQuizzes()->updateExistingPivot($quizId, [
            'is_locked' => false
        ]);

        return response()->json([
            'message' => 'Questionário desbloqueado com sucesso para o aluno.',
            'is_locked' => false
        ]);
    }
}
