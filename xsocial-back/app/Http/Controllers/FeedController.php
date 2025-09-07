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
