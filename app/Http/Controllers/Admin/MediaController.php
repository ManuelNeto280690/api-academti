<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Handle the file upload.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // 2MB Max
            'type' => 'nullable|string|in:courses,users,mentors,certificates'
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $type = $request->input('type', 'general');
            
            // Generate a unique filename
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Store in public disk under the specified type folder
            $path = $file->storeAs("uploads/{$type}", $filename, 'public');
            
            // Get the absolute public URL
            $url = asset(Storage::url($path));

            return response()->json([
                'message' => 'Upload realizado com sucesso',
                'path' => $path,
                'url' => $url,
            ], 200);
        }

        return response()->json([
            'message' => 'Nenhum ficheiro detetado'
        ], 400);
    }

    /**
     * Delete a file from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string'
        ]);

        $path = $request->path;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return response()->json(['message' => 'Ficheiro removido com sucesso']);
        }

        return response()->json(['message' => 'Ficheiro não encontrado'], 404);
    }
}
