<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::with([
            'user:id,name,email,phone', 
            'course:id,title,modalidade,preco_normal,category_id', 
            'course.category:id,name',
            'certification:id,title,type,price,category_id',
            'certification.category:id,name'
        ]);

        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->has('certification_id')) {
            $query->where('certification_id', $request->certification_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }

        return response()->json($query->latest()->paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'status' => 'required|string|in:ativo,concluido,cancelado,pendente',
        ]);

        // Check if already enrolled
        $exists = Enrollment::where('user_id', $validated['user_id'])
            ->where('course_id', $validated['course_id'])
            ->first();

        if ($exists) {
            return response()->json(['message' => 'Este utilizador já está inscrito neste curso.'], 422);
        }

        $enrollment = Enrollment::create($validated);

        return response()->json([
            'message' => 'Inscrição realizada com sucesso.',
            'enrollment' => $enrollment->load(['user', 'course'])
        ], 201);
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:ativo,concluido,cancelado,pendente',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        $statusChanged = $enrollment->status !== $validated['status'];
        $enrollment->update($validated);

        if ($validated['status'] === 'ativo' && $statusChanged) {
            // Check if transaction already exists for this enrollment
            $transactionExists = \App\Models\Transaction::where('user_id', $enrollment->user_id)
                ->where('item_id', $enrollment->course_id ?? $enrollment->certification_id)
                ->exists();

            if (!$transactionExists) {
                $amount = 0;
                $trainerId = null;
                $type = 'Desconhecido';
                $commissionPercent = 0;

                if ($enrollment->course_id) {
                    $course = \App\Models\Course::find($enrollment->course_id);
                    $amount = $course->preco_promocional > 0 ? $course->preco_promocional : $course->preco_normal;
                    $trainerId = $course->trainer_id;
                    $type = 'Curso';
                    $commissionPercent = (float) \App\Models\Setting::get('trainer_commission_course', 70);
                } elseif ($enrollment->certification_id) {
                    $cert = \App\Models\Certification::find($enrollment->certification_id);
                    $amount = $cert->price ?? 0;
                    $trainerId = null; // Certifications might not have a trainer, adjust if needed
                    $type = 'Certificação';
                    $commissionPercent = (float) \App\Models\Setting::get('trainer_commission_certification', 60);
                }

                if ($amount > 0) {
                    $trainerAmount = $trainerId ? ($amount * ($commissionPercent / 100)) : 0;
                    $platformAmount = $amount - $trainerAmount;

                    \App\Models\Transaction::create([
                        'user_id' => $enrollment->user_id,
                        'type' => $type,
                        'item_id' => $enrollment->course_id ?? $enrollment->certification_id,
                        'amount' => $amount,
                        'payment_method' => $enrollment->payment_method ?? 'Manual',
                        'trainer_id' => $trainerId,
                        'trainer_amount' => $trainerAmount,
                        'platform_amount' => $platformAmount,
                        'status' => 'concluído',
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'Inscrição atualizada com sucesso.',
            'enrollment' => $enrollment->load(['user', 'course'])
        ]);
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();
        return response()->json(['message' => 'Inscrição removida com sucesso.']);
    }
}
