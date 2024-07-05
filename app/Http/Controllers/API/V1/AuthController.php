<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationToken;
use App\StreamingServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Login Endpoint
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = \auth('api')->attempt($credentials)) {

            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Endpoint to logout
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        \auth('api')->logout();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    /**
     * Endpoint to register an account
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'streaming_service'     => ['required', Rule::enum(StreamingServices::class)],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user = User::query()->create($request->all());

        return response()->json([
            'user'      => $user
        ], 201);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyAccount(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string', 'exists:verification_tokens,token'],
        ]);

        $token =VerificationToken::query()->where('token', $request->input("token"))->firstOrFail();
        $user = $token->user;

        $token->delete();
        $user->update([
            'email_verified_at' => now()
        ]);

        return response()->json(['message' => 'Your token has been verified.']);
    }

    /**
     * Refreshes your token
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(JWTAuth::refresh());
    }

    /**
     * Returns the token along with its expiry date
     *
     * @param string $token
     * @return JsonResponse
     */
    public function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }

    /**
     * Returns current logged in user
     *
     * @return JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }
}
