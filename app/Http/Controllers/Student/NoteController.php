<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{
    public function index($lessonId)
    {
        $user = Auth::user();
        $notes = Note::where('user_id', $user->id)
            ->where('lesson_id', $lessonId)
            ->get();
            
        return response()->json($notes);
    }

    public function courseNotes($course)
    {
        $user = Auth::user();
        $id = is_object($course) ? $course->id : $course;
        
        Log::info('Buscando notas para curso:', ['course_id' => $id, 'user_id' => $user->id]);

        $notes = Note::where('user_id', $user->id)
            ->whereHas('lesson.module', function($query) use ($id) {
                $query->where('course_id', $id);
            })
            ->with(['lesson' => function($q) {
                $q->select('id', 'title', 'module_id');
            }])
            ->orderBy('created_at', 'desc')
            ->get();
            
        Log::info('Notas encontradas:', ['count' => $notes->count(), 'notes' => $notes->toArray()]);
            
        return response()->json($notes);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'lesson_id' => 'required|uuid|exists:lessons,id',
            'content' => 'required|string',
        ]);

        if (strip_tags($validated['content']) !== $validated['content']) {
            return response()->json([
                'message' => 'Uso de tags HTML não é permitido nas anotações.'
            ], 422);
        }

        $note = Note::create([
            'user_id' => $user->id,
            'lesson_id' => $validated['lesson_id'],
            'content' => trim($validated['content']),
        ]);

        return response()->json([
            'message' => 'Nota salva com sucesso!',
            'note' => $note
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $note = Note::where('user_id', $user->id)->findOrFail($id);
        $note->delete();

        return response()->json(['message' => 'Nota excluída com sucesso!']);
    }
}
