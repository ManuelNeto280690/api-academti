<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = User::role('student')->first() ?? User::factory()->create()->assignRole('student');
        $trainer = User::role('trainer')->first() ?? User::factory()->create()->assignRole('trainer');
        $course = Course::first();

        // 1. Transaction de Venda de Curso no mês atual
        Transaction::create([
            'user_id' => $student->id,
            'amount' => 15000,
            'platform_amount' => 12000,
            'trainer_amount' => 3000,
            'type' => 'Venda de Curso',
            'status' => 'Concluído',
            'payment_method' => 'Multicaixa Express',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 2. Transaction no mês passado
        Transaction::create([
            'user_id' => $student->id,
            'amount' => 12000,
            'platform_amount' => 9600,
            'trainer_amount' => 2400,
            'type' => 'Venda de Curso',
            'status' => 'Concluído',
            'payment_method' => 'Transferência Bancária',
            'created_at' => Carbon::now()->subMonth(),
            'updated_at' => Carbon::now()->subMonth(),
        ]);

        // 3. Mentoria
        Transaction::create([
            'user_id' => $student->id,
            'amount' => 30000,
            'platform_amount' => 24000,
            'trainer_amount' => 6000,
            'type' => 'Mentoria',
            'status' => 'Concluído',
            'payment_method' => 'Cartão',
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        // 4. Pagamento a Formador (Payout)
        Transaction::create([
            'user_id' => $trainer->id,
            'amount' => 5000,
            'platform_amount' => 0,
            'trainer_amount' => 0,
            'type' => 'Pagamento de Comissão',
            'status' => 'Concluído',
            'payment_method' => 'Transferência Bancária',
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now()->subDays(1),
        ]);
        
        // 5. Pagamento pendente
        Transaction::create([
            'user_id' => $student->id,
            'amount' => 8000,
            'platform_amount' => 6400,
            'trainer_amount' => 1600,
            'type' => 'Venda de Certificação',
            'status' => 'Pendente',
            'payment_method' => 'Referência Multicaixa',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
