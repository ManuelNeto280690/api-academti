<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class MentorController extends Controller
{
    public function index()
    {
        // Get all users with the 'mentor' role who are active
        $mentors = User::role('mentor')
            ->where('status', 'ativo')
            ->with('mentorProfile')
            ->get()
            ->map(function ($mentor) {
                $profile = $mentor->mentorProfile;
                return [
                    'id' => $mentor->id,
                    'nome' => $mentor->name,
                    'titulo' => 'Mentor', // Adjust if you add a title field later
                    'empresa' => 'Freelancer', // Default
                    'especialidades' => $profile && $profile->expertise ? $profile->expertise : [],
                    'experiencia' => '5+ anos', // Default or could be added to DB
                    'rating' => 5.0, // Mocked rating
                    'sessoes' => 0, // Mocked sessions
                    'preco' => $profile ? number_format((float)$profile->price_per_session, 2, ',', '.') : '0,00',
                    'periodo' => 'hora',
                    'avatar' => strtoupper(substr($mentor->name, 0, 2)),
                    'localizacao' => 'Remoto',
                    'idiomas' => ['Português'],
                    'bio' => $profile ? $profile->bio : 'Mentor experiente disposto a partilhar conhecimento.',
                    'areas' => $profile && $profile->expertise ? $profile->expertise : [],
                    'disponibilidade' => ['Segunda a Sexta'],
                ];
            });

        return response()->json($mentors);
    }
}
