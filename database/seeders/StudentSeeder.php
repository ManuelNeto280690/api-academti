<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::all();
        if ($courses->isEmpty()) {
            $this->call(CourseSeeder::class);
            $courses = Course::all();
        }

        for ($i = 1; $i <= 15; $i++) {
            $email = "aluno{$i}@exemplo.ao";
            $student = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => "Aluno Teste {$i}",
                    'password' => 'elite@2025',
                    'role' => 'student',
                    'phone' => '+244 923 000 001' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'status' => $i % 5 === 0 ? 'suspenso' : 'ativo'
                ]
            );

            // Se for novo ou não tiver papeis, atribuir 'student'
            if (!$student->hasRole('student')) {
                $student->assignRole('student');
            }

            // Atribuir 1 a 2 cursos aleatórios
            $randomCourses = $courses->random(rand(1, 2));
            foreach ($randomCourses as $course) {
                Enrollment::updateOrCreate(
                    ['user_id' => $student->id, 'course_id' => $course->id],
                    ['progress' => rand(0, 80), 'status' => 'ativo']
                );
            }
        }
    }
}
