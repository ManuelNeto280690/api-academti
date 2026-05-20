<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Certification;
use App\Models\UserCertification;
use App\Models\CertificateTemplate;

class CertificateController extends Controller
{
    public function show(Request $request, $courseId)
    {
        $token = $request->query('token');
        if ($token) {
            $user = \Laravel\Sanctum\PersonalAccessToken::findToken($token)?->tokenable;
            if ($user) {
                Auth::login($user);
            }
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }
        
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('status', 'concluido')
            ->first();

        if (!$enrollment) {
            return abort(403, 'Certificado indisponível ou curso não concluído.');
        }

        $course = Course::with(['certificateTemplate', 'trainer'])->findOrFail($courseId);
        $template = $course->certificateTemplate ?? \App\Models\CertificateTemplate::where('is_default', true)->first();
        $certificate_code = strtoupper(substr(md5($enrollment->id), 0, 12));

        // Signature and color defaults
        $data = [
            'student_name' => $user->name,
            'student_bi' => $user->bi_id ?? 'N/A',
            'course_name' => $course->title,
            'course_duration' => $course->duration_hours ?? 'Estimada',
            'course_level' => $course->level ?? 'Especialização',
            'completion_date' => $enrollment->updated_at->format('d/m/Y'),
            'duration' => $course->duration_hours ? $course->duration_hours . ' Horas' : '',
            'certificate_code' => $certificate_code,
            'instructor_name' => $course->trainer->name ?? ($template?->signature_name ?? 'Manuel Neto'),
            'instructor_title' => $template?->signature_title ?? 'Instrutor Responsável',
            'trainer_name' => $course->trainer->name ?? 'Instrutor Ceftic',
            'verification_url' => url("/verify-certificate/{$certificate_code}"),
            'primary_color' => $template?->primary_color ?? '#ee0204',
            'secondary_color' => $template?->secondary_color ?? '#1a1a1a',
            'background_image' => $template?->background_image,
            'signature_image' => $template?->signature_image,
            'show_logo' => $template?->show_logo ?? true,
            'font_family' => $template?->font_family ?? 'Outfit',
            'layout' => $template?->layout ?? [],
        ];

        return view('certificate', $data);
    }

    public function showCertification(Request $request, $certificationId)
    {
        $token = $request->query('token');
        if ($token) {
            $user = \Laravel\Sanctum\PersonalAccessToken::findToken($token)?->tokenable;
            if ($user) {
                Auth::login($user);
            }
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }
        
        $userCert = UserCertification::where('user_id', $user->id)
            ->where('certification_id', $certificationId)
            ->first();

        if (!$userCert) {
            return abort(403, 'Certificação não encontrada para este utilizador.');
        }

        $cert = Certification::with(['certificateTemplate'])->findOrFail($certificationId);
        $template = $cert->certificateTemplate ?? CertificateTemplate::where('is_default', true)->first();

        // Data mapping
        $data = [
            'student_name' => $user->name,
            'student_bi' => $user->bi_id ?? 'N/A',
            'course_name' => $cert->title,
            'course_duration' => $cert->duration_hours ? $cert->duration_hours . ' Horas' : ($cert->type ?? 'Especialização'),
            'course_level' => $cert->level ?? 'Certificação Profissional',
            'completion_date' => $userCert->issue_date->format('d/m/Y'),
            'duration' => $cert->duration_hours ? $cert->duration_hours . ' Horas' : '',
            'certificate_code' => $userCert->certificate_code,
            'instructor_name' => $template?->signature_name ?? 'Manuel Neto',
            'instructor_title' => $template?->signature_title ?? 'Diretor Geral',
            'trainer_name' => 'Ceftic Elite Certification',
            'verification_url' => url("/verify-certificate/{$userCert->certificate_code}"),
            'primary_color' => $template?->primary_color ?? '#ee0204',
            'secondary_color' => $template?->secondary_color ?? '#1a1a1a',
            'background_image' => $template?->background_image,
            'signature_image' => $template?->signature_image,
            'show_logo' => $template?->show_logo ?? true,
            'font_family' => $template?->font_family ?? 'Outfit',
            'layout' => $template?->layout ?? [],
        ];

        return view('certificate', $data);
    }
}
