<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoursePlayerController extends Controller
{
    public function show($id)
    {
        $user = Auth::user();
        
        // 1. Try to find Course by ID or Slug
        $course = Course::where('id', $id)
            ->orWhere('slug', $id)
            ->with([
                'modules' => fn($q) => $q->orderBy('order'),
                'modules.materials',
                'modules.lessons' => fn($q) => $q->orderBy('order'),
                'modules.lessons.materials',
                'modules.quizzes.questions.options'
            ])
            ->first();

        if ($course) {
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if ((!$enrollment || !in_array($enrollment->status, ['ativo', 'concluido'])) && floatval($course->preco_normal) > 0) {
                 return response()->json(['message' => 'Acesso negado ao curso.', 'status' => 'unauthorized'], 403);
            }

            $this->syncEnrollmentProgress($user, $course);
            
            return response()->json([
                'course' => $course,
                'completed_lessons' => $user->completedLessons()->whereIn('lesson_id', $course->lessons()->pluck('lessons.id'))->pluck('lesson_id')->toArray(),
                'completed_quizzes' => $this->getCompletedQuizzes($user, $course),
                'enrollment_status' => $enrollment ? $enrollment->status : 'none',
                'global_certificate_enabled' => Setting::get('course_certificate_enabled', '1') !== '0'
            ]);
        }

        // 2. Try to find Certification by ID
        $certification = \App\Models\Certification::where('id', $id)
            ->with([
                'modules' => fn($q) => $q->orderBy('order'),
                'modules.materials',
                'modules.lessons' => fn($q) => $q->orderBy('order'),
                'modules.lessons.materials',
                'modules.quizzes.questions.options'
            ])
            ->first();

        if ($certification) {
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('certification_id', $certification->id)
                ->first();

            if ((!$enrollment || !in_array($enrollment->status, ['ativo', 'concluido'])) && floatval($certification->price) > 0) {
                 return response()->json(['message' => 'Acesso negado à certificação.', 'status' => 'unauthorized'], 403);
            }

            $this->syncEnrollmentProgress($user, null, $certification);

            return response()->json([
                'course' => $certification, // Map to 'course' for frontend compatibility
                'is_certification' => true,
                'completed_lessons' => $user->completedLessons()->whereIn('lesson_id', \App\Models\Lesson::whereIn('module_id', $certification->modules()->pluck('id'))->pluck('id'))->pluck('lesson_id')->toArray(),
                'completed_quizzes' => $this->getCompletedQuizzes($user, null, $certification),
                'enrollment_status' => $enrollment ? $enrollment->status : 'none',
                'global_certificate_enabled' => true
            ]);
        }

        return response()->json(['message' => 'Item não encontrado.'], 404);
    }

    private function getCompletedQuizzes($user, $course = null, $certification = null)
    {
        $query = $user->completedQuizzes();
        if ($course) {
            $query->whereIn('quiz_id', $course->quizzes()->pluck('quizzes.id'))
                  ->orWhereIn('quiz_id', $course->modules()->with('quizzes')->get()->pluck('quizzes.*.id')->flatten()->filter());
        } elseif ($certification) {
            $moduleQuizIds = $certification->modules()->with('quizzes')->get()->pluck('quizzes.*.id')->flatten()->filter();
            $query->whereIn('quiz_id', $moduleQuizIds);
        }

        return $query->get()->mapWithKeys(function ($quiz) {
            return [$quiz->id => [
                'score' => $quiz->pivot->score,
                'is_locked' => (bool)$quiz->pivot->is_locked,
            ]];
        });
    }

    public function submitQuiz(Request $request, $quizId)
    {
        $user = Auth::user();
        $quiz = Quiz::with('questions.options')->findOrFail($quizId);
        $answers = $request->input('answers', []); // ['question_id' => 'option_id']
        
        $totalPoints = 0;
        $earnedPoints = 0;

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;
            
            $submittedOptionId = $answers[$question->id] ?? null;
            if ($submittedOptionId) {
                $correctOption = $question->options->where('is_correct', true)->first();
                if ($correctOption && $correctOption->id === $submittedOptionId) {
                    $earnedPoints += $question->points;
                }
            }
        }

        $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100) : 0;

        // Save result and lock
        $user->completedQuizzes()->syncWithoutDetaching([
            $quiz->id => [
                'score' => $score,
                'status' => 'completed',
                'is_locked' => true,
                'completed_at' => now(),
            ]
        ]);

        // Sync progress with Enrollment
        $parent = $quiz->course ?: ($quiz->module ? $quiz->module->course : null);
        $certification = $quiz->certification ?: ($quiz->module ? $quiz->module->certification : null);
        $this->syncEnrollmentProgress($user, $parent, $certification);

        return response()->json([
            'message' => 'Quiz submetido com sucesso! O questionário está agora bloqueado.',
            'score' => $score,
            'is_locked' => true
        ]);
    }

    public function toggleLesson(Request $request, $lessonId)
    {
        $user = Auth::user();
        
        if ($user->completedLessons()->where('lesson_id', $lessonId)->exists()) {
            $user->completedLessons()->detach($lessonId);
            return response()->json(['message' => 'Aula marcada como não concluída.', 'completed' => false]);
        } else {
            $user->completedLessons()->attach($lessonId, ['completed_at' => now()]);
            $message = 'Aula concluída!';
            $completed = true;
        }

        // Sync progress with Enrollment
        $lesson = Lesson::with(['module.course', 'module.certification'])->find($lessonId);
        if ($lesson && $lesson->module) {
            $this->syncEnrollmentProgress($user, $lesson->module->course, $lesson->module->certification);
        }

        return response()->json(['message' => $message, 'completed' => $completed]);
    }

    private function syncEnrollmentProgress($user, $course = null, $certification = null)
    {
        if ($course) {
            $lessonIds = $course->lessons()->pluck('lessons.id');
            $moduleQuizIds = $course->modules()->with('quizzes')->get()
                ->pluck('quizzes.*.id')->flatten()->filter()->unique();
            $standaloneQuizIds = $course->quizzes()->pluck('quizzes.id');
            $quizIds = $moduleQuizIds->concat($standaloneQuizIds)->unique();
            
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->whereIn('status', ['ativo', 'concluido'])
                ->first();
        } elseif ($certification) {
            $moduleIds = $certification->modules()->pluck('id');
            $lessonIds = \App\Models\Lesson::whereIn('module_id', $moduleIds)->pluck('id');
            $quizIds = \App\Models\Quiz::whereIn('module_id', $moduleIds)->pluck('id');
            
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('certification_id', $certification->id)
                ->whereIn('status', ['ativo', 'concluido'])
                ->first();
        } else {
            return;
        }

        // 2. Get total items
        $totalItems = $lessonIds->count() + $quizIds->count();

        // 3. Get completed items
        $completedLessonsCount = $user->completedLessons()
            ->whereIn('lesson_id', $lessonIds)
            ->count();
        
        $completedQuizzesCount = $user->completedQuizzes()
            ->whereIn('quiz_id', $quizIds)
            ->count();

        $completedCount = $completedLessonsCount + $completedQuizzesCount;

        // 4. Calculate percentage
        $progress = $totalItems > 0 ? round(($completedCount / $totalItems) * 100) : 0;

        // 5. Update Enrollment
        if ($enrollment) {
            $status = $enrollment->status;
            if ($progress >= 100) {
                $status = 'concluido';
            } elseif ($status === 'concluido' && $progress < 100) {
                $status = 'ativo';
            }

            $enrollment->update([
                'progress' => $progress,
                'status' => $status
            ]);
        }
    }
}
