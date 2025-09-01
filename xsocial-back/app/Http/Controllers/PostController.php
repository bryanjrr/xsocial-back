<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\ContentType;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class PostController extends Controller
{
    public function makeUserPost(Request $request)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:280',
                'gif' => 'nullable|url',
                'media' => 'nullable|file|mimes:jpeg,png,webp,gif|max:10240',
                'type' => 'nullable|in:gif,photo',
            ]);

            $user = PersonalAccessToken::findToken($request->bearerToken())?->tokenable;
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'No autenticado'], 401);
            }

            $post = $user->post()->create(['content' => $request->content]);

            if ($request->has('gif') && $request->gif) {
                $contentType = ContentType::where('name', 'gif')->firstOrFail();
                $post->mediaPosts()->create([
                    'file_url' => $request->gif,
                    /*  'media_type' => 'App\\Models\\Post', */
                    'content_type' => $contentType->id, // ID 1 (gif)
                ]);
            } elseif ($request->hasFile('media')) {
                $file = $request->file('media');
                $contentTypeName = $request->input('type', in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp']) ? 'photo' : 'gif');
                $contentType = ContentType::where('name', $contentTypeName)->firstOrFail();
                $post->mediaPosts()->create([
                    'file_url' => file_get_contents($file->getRealPath()),
                    /*                     'media_type' => 'App\\Models\\Post',
 */
                    'content_type' => $contentType->id, // ID 3 (photo) o 1 (gif)
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Post published successfully!',
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creando post: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
