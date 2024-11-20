<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\Member;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // dd($request);
        try {
            $request->validate([
                'phone_number' => 'required',
                'password' => 'required'
            ]);
            $user = [
                'phone_number' => $request->input('phone_number'),
                'password' => $request->input('password')
            ];
            if (!$token = JWTAuth::attempt($user)) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Incorrect phonenumber or Password!'
                ], 200);
            }
            $user = request()->user();
            // dd($user);
            $userStatus = $user->enabled;
            $olderToken = $user->token;
            if (!$userStatus) {
                // dd($userStatus);
                JWTAuth::manager()->invalidate(new \Tymon\JWTAuth\Token($olderToken, $forceForever = false));
                return response()->json([
                    'code' => 403,
                    'message' => 'Your account is not active, Please contact admin!'
                ]);
            };
            $userId = $user->id;
            if ($olderToken) {
                JWTAuth::manager()->invalidate(new \Tymon\JWTAuth\Token($olderToken, $forceForever = false));
            }
            User::where('id', $userId)
                ->update([
                    'token' => $token,
                    'fcm_id' => $request->fcm_id
                ]);
            $memberPhone = $user->phone_number;
            $memberId = Member::where('phone', $memberPhone)->pluck('id')->first();
            $userData = [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "phone_number" => $user->phone_number,
                "gender" => $user->gender,
                "role" => $user->getRoleNames()->first(),
                // "roles" => $user->getRoleNames()->toArray(),
                "enabled" => $user->enabled,
                "member_id" => $memberId
            ];
            $tokenData = $this->respondWithToken($token);
            return response()->json([
                'message' => "Logged in successfully!",
                'code' => 200,
                'user' => $userData,
                'token_type' => 'Bearer',
                'token' => $tokenData->original['access_token'],
                'fcm_id' => $request->fcm_id
            ]);
        } catch (Exception $error) {
            // dd($error);
            return response()->json([
                'code' => 500,
                'message' => 'Unable to process your request,Please try again!',
                "error" => $error->getMessage()
            ]);
        }
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json(auth()->user());
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            auth()->logout();
            Session::flush();
            return response()->json([
                'message' => "Successfully logged out",
                'code' => 200
            ]);
        } catch (Exception $error) {
            return response()->json([
                'code' => 500,
                'message' => 'Logout failed',
                "error" => $error->getMessage()
            ]);
        }
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            //'expires_in' => auth()->factory()->getTTL() * 60 * 10000000000
            'expires_in' => null,
            'user' => auth()->user()
        ]);
    }
}