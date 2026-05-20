<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Criar Categorias se não existirem
        $cats = ['Desenvolvimento Web', 'Design Gráfico', 'Marketing Digital', 'Gestão de Projetos'];
        foreach ($cats as $c) {
            Category::updateOrCreate(['name' => $c], ['slug' => Str::slug($c)]);
        }

        $categories = Category::all();
        $trainer = User::role('trainer')->first() ?? User::role('admin')->first();

        // Debugging
        if (!$trainer || $categories->isEmpty()) {
             throw new \Exception("Trainer or Categories not found!");
        }

        // 2. Criar Cursos (Ajustando modalidade para valores do ENUM na migração)
        
        Course::updateOrCreate(
            ['slug' => 'laravel-12-iniciantes'],
            [
                'category_id' => $categories[0]->id,
                'trainer_id' => $trainer->id,
                'title' => 'Laravel 12 para Iniciantes',
                'description' => 'Aprenda o melhor framework PHP do zero.',
                'modalidade' => 'ao-vivo',
                'preco_normal' => 25000.00,
                'status' => 'publicado',
                'rating' => 4.8
            ]
        );

        Course::updateOrCreate(
            ['slug' => 'ui-ux-design-masterclass'],
            [
                'category_id' => $categories[1]->id,
                'trainer_id' => $trainer->id,
                'title' => 'UI/UX Design Masterclass',
                'description' => 'Transforme a sua criatividade em protótipos de alta fidelidade.',
                'modalidade' => 'presencial',
                'preco_normal' => 45000.00,
                'status' => 'publicado',
                'rating' => 4.9
            ]
        );

        Course::updateOrCreate(
            ['slug' => 'gestao-trafego-pago'],
            [
                'category_id' => $categories[2]->id,
                'trainer_id' => $trainer->id,
                'title' => 'Gestão de Tráfego Pago',
                'description' => 'Domine o Facebook e Google Ads.',
                'modalidade' => 'online',
                'preco_normal' => 30000.00,
                'status' => 'publicado',
                'rating' => 4.5
            ]
        );
    }
}
