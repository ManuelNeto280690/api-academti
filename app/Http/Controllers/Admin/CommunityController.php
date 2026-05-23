<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CommunityPost;
use App\Models\User;

class CommunityController extends Controller
{
    public function index()
    {
        $posts = CommunityPost::with(['user:id,name,email', 'replies'])
            ->withCount('replies')
            ->latest()
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'author' => $post->user->name,
                    'title' => $post->title,
                    'category' => $post->category,
                    'replies' => $post->replies_count,
                    'status' => $post->status,
                    'pinned' => $post->pinned,
                    'reported' => $post->reported_count > 0,
                    'date' => $post->created_at->format('d M, Y')
                ];
            });

        // Summary Stats
        $stats = [
            'total_posts' => CommunityPost::count(),
            'total_members' => User::count(),
            'active_discussions' => CommunityPost::where('status', 'ativo')->count(),
            'spam_reported' => CommunityPost::where('reported_count', '>', 0)->count()
        ];

        return response()->json([
            'posts' => $posts,
            'stats' => $stats
        ]);
    }

    public function approve($id)
    {
        $post = CommunityPost::findOrFail($id);
        $post->status = 'ativo';
        $post->reported_count = 0; // reset report status
        $post->save();

        return response()->json(['message' => 'Post aprovado com sucesso.']);
    }

    public function pin($id)
    {
        $post = CommunityPost::findOrFail($id);
        $post->pinned = !$post->pinned;
        $post->save();

        return response()->json(['message' => $post->pinned ? 'Post fixado.' : 'Post desafixado.']);
    }

    public function destroy($id)
    {
        $post = CommunityPost::findOrFail($id);
        $post->delete();

        return response()->json(['message' => 'Post eliminado com sucesso.']);
    }

    public function banUser($postId)
    {
        // Aqui pegamos o autor do post e "banimos" ou apenas marcamos o post como de banido
        $post = CommunityPost::findOrFail($postId);
        $post->status = 'banido';
        $post->save();
        
        // Em um sistema real, você alteraria algo na tabela Users
        // $user = $post->user;
        // $user->status = 'banido';
        // $user->save();

        return response()->json(['message' => 'Utilizador banido (demonstração).']);
    }
}
