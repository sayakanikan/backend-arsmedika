<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use stdClass;

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
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response_json(401, 'failed', 'Unauthorized');
        }

        return response_json(200, 'success', $this->respondWithToken($token));
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        if (!auth()->user()) {
            return response_json(401, 'failed', 'You are not logged in');
        }

        return response_json(200, 'success', auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        if (!auth()->user()) {
            return response_json(401, 'failed', 'You are not logged in');
        }

        auth()->logout();

        return response_json(200, 'success', 'Successfully logged out');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        if (!auth()->user()) {
            return response_json(401, 'failed', 'You are not logged in');
        }
        
        return response_json(200, 'success', $this->respondWithToken(auth()->refresh()));
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
        $result = new stdClass;

        $result = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];

        return $result;
    }
}
