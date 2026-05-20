<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function show(Quiz $quiz)
    {
        return response()->json($quiz->load('questions.options'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'nullable|uuid|exists:courses,id',
            'certification_id' => 'nullable|uuid|exists:certifications,id',
            'module_id' => 'nullable|uuid|exists:modules,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'min_score' => 'nullable|integer|min:0|max:100',
        ]);

        $quiz = Quiz::create($validated);

        return response()->json($quiz, 201);
    }

    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'min_score' => 'nullable|integer|min:0|max:100',
            'questions' => 'nullable|array',
            'questions.*.id' => 'nullable|uuid',
            'questions.*.text' => 'required|string',
            'questions.*.points' => 'required|integer',
            'questions.*.order' => 'required|integer',
            'questions.*.options' => 'required|array|min:1',
            'questions.*.options.*.id' => 'nullable|uuid',
            'questions.*.options.*.text' => 'required|string',
            'questions.*.options.*.is_correct' => 'required|boolean',
        ]);

        DB::transaction(function () use ($quiz, $validated) {
            $quiz->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'min_score' => $validated['min_score'] ?? 70,
            ]);

            if (isset($validated['questions'])) {
                $existingQuestionIds = [];

                foreach ($validated['questions'] as $qData) {
                    $question = $quiz->questions()->updateOrCreate(
                        ['id' => $qData['id'] ?? null],
                        [
                            'text' => $qData['text'],
                            'points' => $qData['points'],
                            'order' => $qData['order'],
                        ]
                    );
                    $existingQuestionIds[] = $question->id;

                    $existingOptionIds = [];
                    foreach ($qData['options'] as $oData) {
                        $option = $question->options()->updateOrCreate(
                            ['id' => $oData['id'] ?? null],
                            [
                                'text' => $oData['text'],
                                'is_correct' => $oData['is_correct'],
                            ]
                        );
                        $existingOptionIds[] = $option->id;
                    }
                    // Delete options not in the request
                    $question->options()->whereNotIn('id', $existingOptionIds)->delete();
                }

                // Delete questions not in the request
                $quiz->questions()->whereNotIn('id', $existingQuestionIds)->delete();
            }
        });

        return response()->json($quiz->load('questions.options'));
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return response()->json(null, 204);
    }
}
