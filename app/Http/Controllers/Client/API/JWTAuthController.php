<?php

namespace App\Http\Controllers\Client\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class JWTAuthController extends Controller
{
    /**
     * Create a new JWTAuthController instance.
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
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        $credentials = request(['email', 'password']);
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        /** @var string $token */
        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function refresh(): JsonResponse
    {
        /** @phpstan-ignore-next-line */
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     * @return JsonResponse
     * @noinspection PhpParamsInspection
     * @noinspection PhpUndefinedMethodInspection
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer', /** @phpstan-ignore-next-line */
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
