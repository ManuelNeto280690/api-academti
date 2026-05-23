<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index()
    {
        // Puxar transações
        $transactions = \App\Models\Transaction::with(['user', 'trainer'])->orderBy('created_at', 'desc')->get();

        $formattedTransactions = $transactions->map(function ($tx) {
            $itemName = 'Desconhecido';
            if ($tx->type === 'Curso' && $tx->item_id) {
                $course = \App\Models\Course::find($tx->item_id);
                if ($course) $itemName = $course->title;
            } elseif ($tx->type === 'Certificação' && $tx->item_id) {
                $cert = \App\Models\Certification::find($tx->item_id);
                if ($cert) $itemName = $cert->title;
            }

            return [
                'id' => substr($tx->id, 0, 8), // Short ID
                'user' => $tx->user ? $tx->user->name : 'Utilizador Removido',
                'type' => $tx->type,
                'item' => $itemName,
                'amount' => '+' . number_format($tx->amount, 0, '', ''),
                'date' => $tx->created_at->format('d M, H:i'),
                'method' => $tx->payment_method ?? 'Cartão',
                'status' => strtolower($tx->status),
            ];
        });

        // Gerar dados do gráfico dinamicamente (ano = 12 meses)
        $anoReceita = array_fill(0, 12, 0);
        $anoComissoes = array_fill(0, 12, 0);
        $anoSubscricoes = array_fill(0, 12, 0);

        foreach ($transactions as $tx) {
            $monthIndex = (int) $tx->created_at->format('n') - 1; // 0 a 11
            $anoReceita[$monthIndex] += (float) $tx->platform_amount; // Receita real para a plataforma
            $anoComissoes[$monthIndex] += (float) $tx->trainer_amount;
            if ($tx->type === 'Subscrição') {
                $anoSubscricoes[$monthIndex] += 1;
            }
        }

        // Mock para trimestres e meses recentes para simplificar a demo, mas com base no ano
        $allData = [
            'mês' => [
                'receita' => array_slice($anoReceita, max(0, date('n') - 6), 6),
                'subscricoes' => array_slice($anoSubscricoes, max(0, date('n') - 6), 6),
                'comissoes' => array_slice($anoComissoes, max(0, date('n') - 6), 6),
            ],
            'trimestre' => [
                'receita' => [array_sum(array_slice($anoReceita,0,3)), array_sum(array_slice($anoReceita,3,3)), array_sum(array_slice($anoReceita,6,3)), array_sum(array_slice($anoReceita,9,3))],
                'subscricoes' => [array_sum(array_slice($anoSubscricoes,0,3)), array_sum(array_slice($anoSubscricoes,3,3)), array_sum(array_slice($anoSubscricoes,6,3)), array_sum(array_slice($anoSubscricoes,9,3))],
                'comissoes' => [array_sum(array_slice($anoComissoes,0,3)), array_sum(array_slice($anoComissoes,3,3)), array_sum(array_slice($anoComissoes,6,3)), array_sum(array_slice($anoComissoes,9,3))],
            ],
            'ano' => [
                'receita' => $anoReceita,
                'subscricoes' => $anoSubscricoes,
                'comissoes' => $anoComissoes,
            ],
        ];

        return response()->json([
            'transactions' => $formattedTransactions,
            'allData' => $allData
        ]);
    }

    public function payout(Request $request)
    {
        $request->validate([
            'trainer_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        \App\Models\Transaction::create([
            'user_id' => auth()->id() ?? $request->trainer_id, // Admin that made the payout or trainer
            'type' => 'Pagamento de Comissão',
            'amount' => -$request->amount,
            'payment_method' => $request->method,
            'trainer_id' => $request->trainer_id,
            'trainer_amount' => -$request->amount,
            'platform_amount' => 0,
            'status' => 'concluído',
        ]);

        return response()->json(['message' => 'Pagamento registado com sucesso.']);
    }
}
