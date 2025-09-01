<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Post;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\User;
use App\Http\Resources\PostResource;
use App\Models\ContentType;
use Illuminate\Support\Facades\Storage;

class FeedController extends Controller
{
    public function makeUserPost(Request $request)
    {
        try {
            $request->validate([
                'content' => 'nullable|string|max:280',
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
                    'content_type' => $contentType->id, // ID 1 (gif)
                ]);
            } elseif ($request->hasFile('media')) {
                $file = $request->file('media');
                $path = $file->store('media', 'public'); // Almacena en storage/app/public/media
                $url = Storage::url($path); // Obtiene la URL pública, e.g., /storage/media/filename

                $mime = $file->getMimeType();
                $contentTypeName = in_array($mime, ['image/jpeg', 'image/png', 'image/webp']) ? 'photo' : 'gif';
                $contentType = ContentType::where('name', $contentTypeName)->firstOrFail();

                $post->mediaPosts()->create([
                    'file_url' => $url,
                    'content_type' => $contentType->id, // ID 3 (photo) o 1 (gif)
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Post creado con éxito',
                'data' => PostResource::make($post->fresh()->load('mediaPosts')),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el post: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $authUser = PersonalAccessToken::findToken($request->bearerToken())?->tokenable;

        if (!$authUser) {
            return response()->json([
                'status' => 'error',
                'message' => 'Usuario no autenticado',
            ], 401);
        }

        $perPage = $request->query('per_page', 10);
        $after = $request->query('after');

        $followedIds = $authUser->followingUsers()->pluck('id_followed')->toArray();

        $query = Post::whereIn('posteable_id', $followedIds)
            ->with([
                'posteable:id_user,username,photo',
                'mediaPosts',
            ])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        if ($after) {
            $query->where('created_at', '<', $after);
        }

        $posts = $query->take($perPage)->get();

        $nextCursor = $posts->last() ? $posts->last()->created_at : null;

        return response()->json([
            'status' => 'success',
            'data' => PostResource::collection($posts),
            'pagination' => [
                'next_cursor' => $nextCursor,
                'per_page' => $perPage,
            ],
        ]);
    }
}
