<?php

namespace App\Http\Controllers;

use App\Models\Account_details;
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
            if ($request->location) {
                $users = User::with('account_details')
                    ->whereHas('account_details', function ($query) use ($request) {
                        $query->where('location', $request->location);
                    })
                    ->withExists(['followersUser as is_following' => function ($query) use ($authUser) {
                        $query->where('id_follower', $authUser->id_user);
                    }])
                    ->whereNot('id_user', $authUser->id_user)
                    ->get();
            } else {
                $users = User::with('account_details')
                    ->withExists(['followersUser as is_following' => function ($query) use ($authUser) {
                        $query->where('id_follower', $authUser->id_user);
                    }])
                    ->whereNot('id_user', $authUser->id_user)
                    ->get();
            }

            return response()->json($users, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function UnfollowUser(Request $request)
    {
        try {
            $authUser = PersonalAccessToken::findToken($request->bearerToken())?->tokenable;
            $authUser->followingUsers()->detach($request->id_followed);
            return response()->json([
                'status' => "success",
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => "error",
                'message' => "Internal server error",
                "details" => $e->getMessage()
            ], 500);
        }
    }
}
