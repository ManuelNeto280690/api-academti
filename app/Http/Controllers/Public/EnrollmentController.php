<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use App\Mail\TrainerEnrollmentRequested;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EnrollmentController extends Controller
{
    public function guestStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:20',
            'course_id' => 'required|exists:courses,id',
            'payment_method' => 'required|string|in:transferencia,presencial',
            'account_opt_in' => 'required|boolean',
        ]);

        // Check if course is "presencial"
        $course = Course::findOrFail($validated['course_id']);
        if (strtolower($course->modalidade) !== 'presencial') {
            return response()->json([
                'message' => 'O registo de convidado só está disponível para cursos presenciais.'
            ], 422);
        }

        // Check if user already exists
        $user = User::where('email', $validated['email'])->first();
        if ($user) {
            return response()->json([
                'message' => 'Este e-mail já está registado na nossa plataforma. Por favor, faça login para se inscrever ou utilize outro e-mail.'
            ], 422);
        }

        // Create user in background
        $password = Str::random(12);
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $password,
            'role' => 'student',
            'status' => 'ativo',
        ]);

        $user->assignRole('student');

        // Note: For now we don't send emails unless Mail is configured.
        // But if account_opt_in is true, we could log that or trigger a password reset mail.

        // Create enrollment - Auto-approve if price is 0
        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $validated['course_id'],
            'payment_method' => $validated['payment_method'],
            'status' => (floatval($course->preco_normal) <= 0 && strtolower($course->modalidade) !== 'presencial') ? 'ativo' : 'pendente',
            'progress' => 0,
        ]);

        $message = 'Inscrição realizada com sucesso! Por favor, aguarde a confirmação do pagamento.';
        if ($validated['account_opt_in']) {
            $message .= ' Uma conta foi criada para si. Receberá instruções de acesso em breve no seu e-mail.';
        }

        return response()->json([
            'message' => $message,
            'enrollment' => $enrollment,
            'account_created' => true,
        ], 201);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'nullable|required_without:certification_id|exists:courses,id',
            'certification_id' => 'nullable|required_without:course_id|exists:certifications,id',
            'payment_method' => 'required|string|in:transferencia,presencial',
        ]);

        $user = Auth::user();
        
        // 1. Course Enrollment
        if ($request->has('course_id')) {
            $courseId = $validated['course_id'];
            $exists = Enrollment::where('user_id', $user->id)->where('course_id', $courseId)->first();
            if ($exists) {
                return response()->json(['message' => 'Você já possui uma inscrição para este curso.', 'status' => $exists->status], 422);
            }

            $course = Course::find($courseId);
            if ($validated['payment_method'] === 'presencial' && strtolower($course->modalidade) !== 'presencial') {
                return response()->json(['message' => 'O pagamento presencial só está disponível para cursos presenciais.'], 422);
            }

            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $courseId,
                'payment_method' => $validated['payment_method'],
                'status' => (floatval($course->preco_normal) <= 0 && strtolower($course->modalidade) !== 'presencial') ? 'ativo' : 'pendente',
                'progress' => 0,
            ]);

            return response()->json([
                'message' => 'Inscrição realizada com sucesso!', 'enrollment' => $enrollment
            ], 201);
        }

        // 2. Certification Enrollment
        if ($request->has('certification_id')) {
            $certId = $validated['certification_id'];
            $exists = Enrollment::where('user_id', $user->id)->where('certification_id', $certId)->first();
            if ($exists) {
                return response()->json(['message' => 'Você já possui uma inscrição para esta certificação.', 'status' => $exists->status], 422);
            }

            $cert = \App\Models\Certification::find($certId);
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'certification_id' => $certId,
                'payment_method' => $validated['payment_method'],
                'status' => (floatval($cert->price) <= 0 && strtolower($cert->type) !== 'presencial') ? 'ativo' : 'pendente',
                'progress' => 0,
            ]);

            return response()->json([
                'message' => 'Inscrição realizada com sucesso!', 'enrollment' => $enrollment
            ], 201);
        }
    }

    public function checkoutStore(Request $request)
    {
        $validated = $request->validate([
            'course_ids' => 'sometimes|array',
            'certification_ids' => 'sometimes|array',
        ]);

        $user = Auth::user();
        $enrollments = [];
        $alreadyEnrolled = [];

        // All IDs to process (consolidated for fallback)
        $courseIds = $validated['course_ids'] ?? [];
        $certIds = $validated['certification_ids'] ?? [];

        // 1. Process explicit Course IDs (with fallback to certification check if not found)
        foreach ($courseIds as $id) {
            // Priority 1: Check if it's a Course
            $course = Course::find($id);
            if ($course) {
                if (Enrollment::where('user_id', $user->id)->where('course_id', $id)->exists()) {
                    $alreadyEnrolled[] = $id; continue;
                }
                $enrollments[] = Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $id,
                    'payment_method' => 'transferencia',
                    'status' => (floatval($course->preco_normal) <= 0 && strtolower($course->modalidade) !== 'presencial') ? 'ativo' : 'pendente',
                    'progress' => 0,
                ]);
                continue;
            }

            // Priority 2: Fallback (check if it was actually a Certification)
            $cert = \App\Models\Certification::find($id);
            if ($cert) {
                if (Enrollment::where('user_id', $user->id)->where('certification_id', $id)->exists()) {
                    $alreadyEnrolled[] = $id; continue;
                }
                $enrollments[] = Enrollment::create([
                    'user_id' => $user->id,
                    'certification_id' => $id,
                    'payment_method' => 'transferencia',
                    'status' => (floatval($cert->price) <= 0 && strtolower($cert->type) !== 'presencial') ? 'ativo' : 'pendente',
                    'progress' => 0,
                ]);
                continue;
            }

            return response()->json(['message' => "Item ID {$id} não encontrado."], 422);
        }

        // 2. Process explicit Certification IDs
        foreach ($certIds as $id) {
            // (Only if not already processed in courseIds)
            if (in_array($id, $courseIds)) continue;

            $cert = \App\Models\Certification::find($id);
            if ($cert) {
                if (Enrollment::where('user_id', $user->id)->where('certification_id', $id)->exists()) {
                    $alreadyEnrolled[] = $id; continue;
                }
                $enrollments[] = Enrollment::create([
                    'user_id' => $user->id,
                    'certification_id' => $id,
                    'payment_method' => 'transferencia',
                    'status' => (floatval($cert->price) <= 0 && strtolower($cert->type) !== 'presencial') ? 'ativo' : 'pendente',
                    'progress' => 0,
                ]);
                continue;
            }
            
            return response()->json(['message' => "Certificação ID {$id} não encontrada."], 422);
        }

        if (count($enrollments) > 0) {
            // Notify trainers (only for courses)
            $courseEnrollments = array_filter($enrollments, fn($e) => !empty($e->course_id));
            if (count($courseEnrollments) > 0) {
                $courses = Course::whereIn('id', array_map(fn($e) => $e->course_id, $courseEnrollments))->get();
                $groupedByTrainer = $courses->groupBy('trainer_id');

                foreach ($groupedByTrainer as $trainerId => $trainerCourses) {
                    $trainer = User::find($trainerId);
                    if ($trainer && $trainer->email) {
                        try {
                            Mail::to($trainer->email)->send(new TrainerEnrollmentRequested($trainer, $user, $trainerCourses->all()));
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("Erro ao enviar email para formador {$trainer->email}: " . $e->getMessage());
                        }
                    }
                }
            }
        }

        $message = count($enrollments) . ' inscrições realizadas com sucesso! Por favor, aguarde a confirmação do pagamento após o envio do comprovativo.';
        if (count($alreadyEnrolled) > 0) {
            $message .= ' (Alguns itens foram ignorados por já possuir inscrição)';
        }

        return response()->json([
            'message' => $message,
            'enrollments' => $enrollments
        ], 201);
    }
}
