<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Models\UserCertification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CertificationController extends Controller
{
    public function index()
    {
        return response()->json(Certification::with('category')->withCount('users')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'type' => 'required|in:Online,Presencial,Semi-presencial,Ao Vivo',
            'duration_hours' => 'integer|min:0',
            'level' => 'nullable|string',
            'price' => 'numeric|min:0',
            'validity_months' => 'nullable|integer',
            'status' => 'required|in:Ativo,Inativo,Rascunho',
            'certificate_template_id' => 'nullable|exists:certificate_templates,id',
            'prerequisites' => 'nullable|array',
            'objectives' => 'nullable|array',
            'exam_format' => 'nullable|array',
            'salary_range' => 'nullable|array',
            'partner_companies' => 'nullable|array',
        ]);

        $certification = Certification::create($validated);
        return response()->json($certification, 201);
    }

    public function show(Certification $certification)
    {
        $user = auth('sanctum')->user();
        $certification->load(['certificateTemplate', 'category', 'quizzes']);

        if ($user) {
            $enrollment = \App\Models\Enrollment::where('user_id', $user->id)
                ->where('certification_id', $certification->id)
                ->first();
            
            $certification->is_enrolled = (bool)$enrollment;
            $certification->enrollment_status = $enrollment ? $enrollment->status : null;
        } else {
            $certification->is_enrolled = false;
            $certification->enrollment_status = null;
        }

        return response()->json($certification);
    }

    public function update(Request $request, Certification $certification)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'type' => 'sometimes|required|in:Online,Presencial,Semi-presencial,Ao Vivo',
            'duration_hours' => 'integer|min:0',
            'level' => 'nullable|string',
            'price' => 'numeric|min:0',
            'validity_months' => 'nullable|integer',
            'status' => 'sometimes|required|in:Ativo,Inativo,Rascunho',
            'certificate_template_id' => 'nullable|exists:certificate_templates,id',
            'prerequisites' => 'nullable|array',
            'objectives' => 'nullable|array',
            'exam_format' => 'nullable|array',
            'salary_range' => 'nullable|array',
            'partner_companies' => 'nullable|array',
        ]);

        $certification->update($validated);
        return response()->json($certification);
    }

    public function destroy(Certification $certification)
    {
        $certification->delete();
        return response()->json(null, 204);
    }

    public function issue(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'certification_id' => 'required|exists:certifications,id',
        ]);

        $code = strtoupper(Str::random(12));
        
        $userCertification = UserCertification::create([
            'user_id' => $validated['user_id'],
            'certification_id' => $validated['certification_id'],
            'certificate_code' => $code,
            'issue_date' => now(),
        ]);

        return response()->json($userCertification, 201);
    }
}
