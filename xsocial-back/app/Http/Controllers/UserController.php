<?php

namespace App\Http\Controllers;

use App\Models\AccountDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getUserByToken(Request $request)
    {
        try {
            $user = PersonalAccessToken::findToken($request->bearerToken())?->tokenable;
            if (!$user) {
                return response()->json([
                    'status' => "error",
                    'message' => "User not found"
                ], 404);
            } else {
                return response()->json([
                    'status' => "success",
                    'user' => $user,
                    'account_details' => $user->account_details
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => "error",
                'message' => "Internal server error",
                "details" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */

    public function login(Request $request)
    {
        try {
            $user = User::where(['email' => $request->email])->first();;
            if ($user && Hash::check($request->password, $user->password)) {
                $user->tokens()->delete();
                $token = $user->createToken($user->username)->plainTextToken;
                return response()->json([
                    'status' => "success",
                    'token' => $token,
                    'message' => "You have logged in successfully."
                ], 200);
            } else {
                return response()->json([
                    'status' => "error",
                    'message' => 'Please check your email and password and try again.'
                ], 401);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => "error",
                'message' => "Internal server error",
                "details" => $e->getMessage()
            ], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'name' => 'required',
                'surname' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            } else {
                $avatarUrl = "https://api.dicebear.com/7.x/thumbs/svg?seed=$request->username";

                $user = User::create([
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'photo' => $avatarUrl,
                ]);

                $user->account_details()->create([
                    'id_user' => $user->id_user,
                    'full_name' => $request->name . " " . $request->surname,
                    'union_date' => Carbon::now(),
                ]);
            }
            return response()->json([
                'user' => $user,
                'status' => "success",
                'message' => "Your account has been created successfully."
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => "error",
                'message' => "Internal server error",
                "details" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
