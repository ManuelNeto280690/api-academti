<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CommunityPost;
use App\Models\CommunityReply;
use App\Models\User;

class CommunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        if ($users->count() < 2) return; // need some users

        $post1 = CommunityPost::create([
            'user_id' => $users->first()->id,
            'title' => 'Como começar com Laravel?',
            'content' => 'Alguém tem dicas para quem está a começar?',
            'category' => 'Dúvidas',
            'status' => 'ativo',
            'pinned' => true,
        ]);

        CommunityReply::create([
            'community_post_id' => $post1->id,
            'user_id' => $users->last()->id,
            'content' => 'Recomendo ver a documentação oficial!',
        ]);

        CommunityPost::create([
            'user_id' => $users->last()->id,
            'title' => 'Problemas com o React Router',
            'content' => 'Estou a ter um erro 404 em produção.',
            'category' => 'Frontend',
            'status' => 'ativo',
            'reported_count' => 2, // reported
        ]);
        
        CommunityPost::create([
            'user_id' => $users->first()->id,
            'title' => 'Comprar curso fora de Angola',
            'content' => 'É possível pagar com cartão Visa internacional?',
            'category' => 'Geral',
            'status' => 'ativo',
        ]);
    }
}
