<?php

namespace App\Http\Controllers;

use App\Models\AccountDetail;
use Exception;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class FollowerController extends Controller
{
    public function showFollowing(Request $request)
    {
        try {
            $authUser = PersonalAccessToken::findToken($request->bearerToken())?->tokenable;
            $alreadyFollowingIds = $authUser->followingUsers()->pluck('id_followed')->toArray();  // ¡Cambio aquí! 'id_followed' en lugar de 'id_user'
            if (empty($alreadyFollowingIds)) {
                $alreadyFollowingIds = [];
            }

            $query = User::with('account_details')
                ->withExists(['followingUsers as is_following' => function ($query) use ($authUser) {  // ¡Cambio aquí! Usa 'followingUsers' y ajusta el where
                    $query->where('id_follower', $authUser->id_user);  // Verifica si tú sigues al usuario (id_follower = auth, id_followed = el usuario)
                }])
                ->whereNot('id_user', $authUser->id_user)
                ->whereNotIn('id_user', $alreadyFollowingIds);

            /*  if ($request->location) {
                $query->whereHas('account_details', function ($q) use ($request) {
                    $q->where('location', $request->location);
                });
            } */

            $perPage = $request->input('per_page', 5);
            $cursor = $request->input('cursor');
            if ($cursor) {
                $query->where('id_user', '>', $cursor);
            }
            $users = $query->limit($perPage + 1)->get();

            $nextCursor = null;
            if ($users->count() > $perPage) {
                $nextCursor = $users[$perPage]->id_user;
                $users = $users->slice(0, $perPage);
            }

            return response()->json([
                'data' => $users->values(),
                'next_cursor' => $nextCursor,
                'per_page' => $perPage,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function UnfollowUser(Request $request)
    {
        try {
            $authUser = PersonalAccessToken::findToken($request->bearerToken())?->tokenable;
            $authUser->followingUsers()->detach($request->input('id_user'));
            return response()->json([
                'status' => "success",
                "user" => $request->input('id_user')
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => "error",
                'message' => "Internal server error",
                "details" => $e->getMessage()
            ], 500);
        }
    }

    public function followUser(Request $request)
    {
        try {
            $authUser = PersonalAccessToken::findToken($request->bearerToken())?->tokenable;
            $isAlreadyFollowing = $authUser->followingUsers()
                ->where('id_followed', $request->id_user)
                ->exists();

            if (!$isAlreadyFollowing) {
                $authUser->followingUsers()->attach($request->id_user);
            }
            $followedUser = User::find($request->id_user);
            return response()->json([
                'status' => "success",
                'message' => "You’re now following " . ($followedUser ? $request->username : ''),
                "request" => $request->all(),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => "error",
                'message' => "Internal server error",
                "details" => $e->getMessage(),
            ], 500);
        }
    }
}
