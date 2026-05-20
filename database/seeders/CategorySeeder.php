<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Desenvolvimento Software', 'slug' => 'desenvolvimento', 'color' => '#3b82f6', 'icon' => 'Code'],
            ['name' => 'Cibersegurança', 'slug' => 'ciberseguranca', 'color' => '#ef4444', 'icon' => 'Shield'],
            ['name' => 'Design & UX/UI', 'slug' => 'design', 'color' => '#ec4899', 'icon' => 'Palette'],
            ['name' => 'Inteligência Artificial', 'slug' => 'ia', 'color' => '#8b5cf6', 'icon' => 'Brain'],
            ['name' => 'Gestão & Negócios', 'slug' => 'gestao', 'color' => '#10b981', 'icon' => 'Briefcase'],
            ['name' => 'Marketing Digital', 'slug' => 'marketing', 'color' => '#f59e0b', 'icon' => 'Megaphone'],
            ['name' => 'Cloud & Infraestrutura', 'slug' => 'cloud', 'color' => '#06b6d4', 'icon' => 'Server'],
        ];

        foreach ($categories as $cat) {
            \App\Models\Category::updateOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
    }
}
