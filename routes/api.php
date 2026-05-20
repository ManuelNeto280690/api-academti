<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Public\CourseController as PublicCourseController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;

// Auth Routes (Public)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

// Public App Routes
Route::get('/courses', [PublicCourseController::class, 'index']);
Route::get('/courses/{course:slug}', [PublicCourseController::class, 'show']);
Route::get('/categories', [\App\Http\Controllers\Public\CategoryController::class, 'index']);
Route::post('/enrollments/guest', [\App\Http\Controllers\Public\EnrollmentController::class, 'guestStore']);
Route::get('/public/settings', [\App\Http\Controllers\Public\SettingController::class, 'index']);
Route::get('/student/courses/{id}/certificate', [\App\Http\Controllers\Student\CertificateController::class, 'show']);
Route::get('/certifications', [\App\Http\Controllers\Admin\CertificationController::class, 'index']);
Route::get('/certifications/{certification}', [\App\Http\Controllers\Admin\CertificationController::class, 'show']);
Route::get('/student/certifications/{id}/certificate', [\App\Http\Controllers\Student\CertificateController::class, 'showCertification']);

// Protected User Routes (Auth Required)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/enrollments', [\App\Http\Controllers\Public\EnrollmentController::class, 'store']);
    Route::post('/enrollments/checkout', [\App\Http\Controllers\Public\EnrollmentController::class, 'checkoutStore']);

    Route::get('/student/dashboard', [\App\Http\Controllers\Student\DashboardController::class, 'index']);
    Route::get('/student/courses/{id}', [\App\Http\Controllers\Student\CoursePlayerController::class, 'show']);
    Route::post('/student/lessons/{lesson}/toggle', [\App\Http\Controllers\Student\CoursePlayerController::class, 'toggleLesson']);
    Route::post('/student/quizzes/{quiz}/submit', [\App\Http\Controllers\Student\CoursePlayerController::class, 'submitQuiz']);

    // Student Notes
    Route::get('/student/lessons/{lesson}/notes', [\App\Http\Controllers\Student\NoteController::class, 'index']);
    Route::get('/student/courses/{course}/notes', [\App\Http\Controllers\Student\NoteController::class, 'courseNotes']);
    Route::post('/student/notes', [\App\Http\Controllers\Student\NoteController::class, 'store']);
    Route::delete('/student/notes/{id}', [\App\Http\Controllers\Student\NoteController::class, 'destroy']);

    // Course Reviews
    Route::post('/student/reviews', [\App\Http\Controllers\Student\ReviewController::class, 'store']);

    // Admin Only Routes
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        // Quiz Locking/Unlocking
        Route::post('/users/{user}/quizzes/{quiz}/unlock', [\App\Http\Controllers\Admin\QuizStudentController::class, 'unlock']);
        // Cursos
        Route::get('/courses', [AdminCourseController::class, 'index'])->middleware('can:ver-cursos');
        Route::post('/courses', [AdminCourseController::class, 'store'])->middleware('can:criar-cursos');
        Route::get('/courses/{course}', [AdminCourseController::class, 'show'])->middleware('can:ver-cursos');
        Route::put('/courses/{course}', [AdminCourseController::class, 'update'])->middleware('can:editar-cursos');
        Route::post('/courses/{course}/approve', [AdminCourseController::class, 'approve'])->middleware('can:aprovar-cursos');
        Route::post('/courses/{course}/reject', [AdminCourseController::class, 'reject'])->middleware('can:aprovar-cursos');
        Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->middleware('can:eliminar-cursos');

        // Configurações
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->middleware('can:gerir-acessos');
        Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->middleware('can:gerir-acessos');

        // Módulos
        Route::get('/courses/{course}/modules', [\App\Http\Controllers\Admin\ModuleController::class, 'index'])->middleware('can:ver-cursos');
        Route::post('/courses/{course}/modules', [\App\Http\Controllers\Admin\ModuleController::class, 'store'])->middleware('can:editar-cursos');
        Route::get('/modules/{module}', [\App\Http\Controllers\Admin\ModuleController::class, 'show'])->middleware('can:ver-cursos');
        Route::put('/modules/{module}', [\App\Http\Controllers\Admin\ModuleController::class, 'update'])->middleware('can:editar-cursos');
        Route::delete('/modules/{module}', [\App\Http\Controllers\Admin\ModuleController::class, 'destroy'])->middleware('can:editar-cursos');

        // Aulas
        Route::get('/modules/{module}/lessons', [\App\Http\Controllers\Admin\LessonController::class, 'index'])->middleware('can:ver-cursos');
        Route::post('/modules/{module}/lessons', [\App\Http\Controllers\Admin\LessonController::class, 'store'])->middleware('can:editar-cursos');
        Route::get('/lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'show'])->middleware('can:ver-cursos');
        Route::put('/lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'update'])->middleware('can:editar-cursos');
        Route::delete('/lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'destroy'])->middleware('can:editar-cursos');

        // Utilizadores
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->middleware('can:ver-utilizadores');
        Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->middleware('can:criar-utilizadores');
        Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->middleware('can:ver-utilizadores');
        Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->middleware('can:editar-utilizadores');
        Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->middleware('can:eliminar-utilizadores');
        Route::post('/users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->middleware('can:suspender-utilizadores');

        // Alunos
        Route::get('/students', [\App\Http\Controllers\Admin\StudentController::class, 'index'])->middleware('can:ver-alunos');
        Route::post('/students', [\App\Http\Controllers\Admin\StudentController::class, 'store'])->middleware('can:criar-alunos');
        Route::get('/students/{student}', [\App\Http\Controllers\Admin\StudentController::class, 'show'])->middleware('can:ver-alunos');
        Route::put('/students/{student}', [\App\Http\Controllers\Admin\StudentController::class, 'update'])->middleware('can:editar-alunos');
        Route::delete('/students/{student}', [\App\Http\Controllers\Admin\StudentController::class, 'destroy'])->middleware('can:eliminar-alunos');

        // Formadores
        Route::get('/trainers', [\App\Http\Controllers\Admin\TrainerController::class, 'index'])->middleware('can:ver-formadores');
        Route::post('/trainers', [\App\Http\Controllers\Admin\TrainerController::class, 'store'])->middleware('can:criar-formadores');
        Route::get('/trainers/{trainer}', [\App\Http\Controllers\Admin\TrainerController::class, 'show'])->middleware('can:ver-formadores');
        Route::put('/trainers/{trainer}', [\App\Http\Controllers\Admin\TrainerController::class, 'update'])->middleware('can:editar-formadores');
        Route::delete('/trainers/{trainer}', [\App\Http\Controllers\Admin\TrainerController::class, 'destroy'])->middleware('can:eliminar-formadores');

        // Mentores
        Route::get('/mentors', [\App\Http\Controllers\Admin\MentorController::class, 'index'])->middleware('can:ver-mentores');
        Route::post('/mentors', [\App\Http\Controllers\Admin\MentorController::class, 'store'])->middleware('can:criar-mentores');
        Route::get('/mentors/{mentor}', [\App\Http\Controllers\Admin\MentorController::class, 'show'])->middleware('can:ver-mentores');
        Route::put('/mentors/{mentor}', [\App\Http\Controllers\Admin\MentorController::class, 'update'])->middleware('can:editar-mentores');
        Route::delete('/mentors/{mentor}', [\App\Http\Controllers\Admin\MentorController::class, 'destroy'])->middleware('can:eliminar-mentores');

        // Empresas
        Route::get('/companies', [\App\Http\Controllers\Admin\CompanyController::class, 'index'])->middleware('can:ver-empresas');
        Route::post('/companies', [\App\Http\Controllers\Admin\CompanyController::class, 'store'])->middleware('can:criar-empresas');
        Route::get('/companies/{company}', [\App\Http\Controllers\Admin\CompanyController::class, 'show'])->middleware('can:ver-empresas');
        Route::put('/companies/{company}', [\App\Http\Controllers\Admin\CompanyController::class, 'update'])->middleware('can:editar-empresas');
        Route::delete('/companies/{company}', [\App\Http\Controllers\Admin\CompanyController::class, 'destroy'])->middleware('can:eliminar-empresas');

        // Categorias
        Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->middleware('can:ver-categorias');
        Route::post('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->middleware('can:criar-categorias');
        Route::get('/categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'show'])->middleware('can:ver-categorias');
        Route::put('/categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->middleware('can:editar-categorias');
        Route::delete('/categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->middleware('can:eliminar-categorias');

        // Roles & Permissions
        Route::get('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->middleware('can:gerir-acessos');
        Route::apiResource('certificate-templates', \App\Http\Controllers\Admin\CertificateTemplateController::class)->middleware('can:gerir-acessos');
        Route::post('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->middleware('can:gerir-acessos');
        Route::put('/roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->middleware('can:gerir-acessos');
        Route::delete('/roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->middleware('can:gerir-acessos');
        
        Route::get('/permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])->middleware('can:gerir-acessos');
        Route::apiResource('enrollments', 'App\Http\Controllers\Admin\EnrollmentController')->middleware('can:editar-cursos');
        Route::get('/enrollments/{enrollment}/quizzes', [\App\Http\Controllers\Admin\QuizStudentController::class, 'index'])->middleware('can:editar-cursos');
        Route::post('/modules/reorder', [\App\Http\Controllers\Admin\ModuleController::class, 'reorder'])->middleware('can:editar-cursos');
        Route::post('/lessons/reorder', [\App\Http\Controllers\Admin\LessonController::class, 'reorder'])->middleware('can:editar-cursos');
        
        // Add route to get users for enrollment selection
        Route::get('/users-list', function() {
            return \App\Models\User::select('id', 'name', 'email')->get();
        })->middleware('can:editar-cursos');

        Route::post('/permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'store'])->middleware('can:gerir-acessos');
        Route::put('/permissions/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'update'])->middleware('can:gerir-acessos');
        Route::delete('/permissions/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'destroy'])->middleware('can:gerir-acessos');
        // Media Uploads
        Route::post('/upload', [\App\Http\Controllers\Admin\MediaController::class, 'upload'])->middleware('can:editar-cursos');

        // Questionários
        Route::post('/quizzes', [\App\Http\Controllers\Admin\QuizController::class, 'store'])->middleware('can:editar-cursos');
        Route::get('/quizzes/{quiz}', [\App\Http\Controllers\Admin\QuizController::class, 'show'])->middleware('can:ver-cursos');
        Route::put('/quizzes/{quiz}', [\App\Http\Controllers\Admin\QuizController::class, 'update'])->middleware('can:editar-cursos');
        Route::delete('/quizzes/{quiz}', [\App\Http\Controllers\Admin\QuizController::class, 'destroy'])->middleware('can:editar-cursos');

        // Certificações
        Route::get('/certifications', [\App\Http\Controllers\Admin\CertificationController::class, 'index'])->middleware('can:ver-certificacoes');
        Route::post('/certifications', [\App\Http\Controllers\Admin\CertificationController::class, 'store'])->middleware('can:criar-certificacoes');
        Route::get('/certifications/{certification}', [\App\Http\Controllers\Admin\CertificationController::class, 'show'])->middleware('can:ver-certificacoes');
        Route::put('/certifications/{certification}', [\App\Http\Controllers\Admin\CertificationController::class, 'update'])->middleware('can:editar-certificacoes');
        Route::delete('/certifications/{certification}', [\App\Http\Controllers\Admin\CertificationController::class, 'destroy'])->middleware('can:eliminar-certificacoes');
        Route::post('/certifications/issue', [\App\Http\Controllers\Admin\CertificationController::class, 'issue'])->middleware('can:emitir-certificados');

        // Módulos para Certificações
        Route::get('/certifications/{certification}/modules', [\App\Http\Controllers\Admin\ModuleController::class, 'certificationModules'])->middleware('can:ver-certificacoes');
        Route::post('/certifications/{certification}/modules', [\App\Http\Controllers\Admin\ModuleController::class, 'storeCertificationModule'])->middleware('can:editar-certificacoes');

        // Materiais de Apoio
        Route::get('/materials', [\App\Http\Controllers\Admin\MaterialController::class, 'index'])->middleware('can:ver-cursos');
        Route::post('/materials', [\App\Http\Controllers\Admin\MaterialController::class, 'store'])->middleware('can:editar-cursos');
        Route::delete('/materials/{material}', [\App\Http\Controllers\Admin\MaterialController::class, 'destroy'])->middleware('can:editar-cursos');
    });
});

Route::get('/status', function () {
    return response()->json([
        'status' => 'online',
        'api' => 'Ceftic Elite API',
        'version' => '1.2.0',
        'laravel' => app()->version()
    ]);
});
