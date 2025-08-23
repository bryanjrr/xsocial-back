<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Post;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\User;

class FeedController extends Controller
{
    public function userPosts(Request $request, $username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $perPage = $request->query('per_page', 10);
        $after = $request->query('after');

        $query = Post::where('user_id', $user->id)
            ->with(['user' => function ($query) {
                $query->select('id', 'username', 'photo');
            }])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        if ($after) {
            $query->where('created_at', '<', $after);
        }

        $posts = $query->take($perPage)->get();
        $nextCursor = $posts->last() ? $posts->last()->created_at : null;

        return response()->json([
            'status' => 'success',
            'data' => $posts,
            'pagination' => [
                'next_cursor' => $nextCursor,
                'per_page' => $perPage,
            ],
        ]);
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

        $followedIds = $authUser->followingUsers()->pluck('followed_id')->toArray();
        dd($followedIds);

        $query = Post::whereIn('user_id', $followedIds)
            ->with(['user' => function ($query) {
                $query->select('id', 'username', 'photo');
            }])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        if ($after) {
            $query->where('created_at', '<', $after);
        }

        $posts = $query->take($perPage)->get();

        $nextCursor = $posts->last() ? $posts->last()->created_at : null;

        return response()->json([
            'status' => 'success',
            'data' => $posts,
            'pagination' => [
                'next_cursor' => $nextCursor,
                'per_page' => $perPage,
            ],
        ]);
    }
}
