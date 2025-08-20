<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Post;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class PostController extends Controller
{
    public function makeUserPost(Request $request)
    {
        try {
            $validated = $request->validate([
                'content' => 'required|string|max:280',
            ]);
            $user = PersonalAccessToken::findToken($request->bearerToken())?->tokenable;
            $post = $user->post()->create(['content' => $request->content]);
            if ($request->gif) {
                $post->media_post()->create([
                    'file_url' => $request->gif
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => "error",
                'message' => "Internal Server Error",
                'details' => $e->getMessage()
            ], 500);
        }
        return response()->json([
            'status' => "success",
            'message' => "Your post has been published successfully."
        ], 200);
    }
}
