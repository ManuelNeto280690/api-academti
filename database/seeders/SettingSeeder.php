<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'plataformaNome' => 'Ceftic',
            'plataformaUrl' => 'https://ceftic.pt',
            'emailSuporte' => 'suporte@ceftic.pt',
            'moeda' => 'Kwanza (Kz)',
            'idioma' => 'Português (PT)',
            'fuso' => 'Africa/Luanda (UTC+1)',
            'course_sequential_unlock' => '0',
            'course_min_pass_score' => '70',
            'course_access_duration_days' => '0',
            'course_certificate_enabled' => '1',
            'registoAtivo' => '1',
            'comunidadePublica' => '1',
            'talentos' => '1',
            'mentorias' => '1',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
