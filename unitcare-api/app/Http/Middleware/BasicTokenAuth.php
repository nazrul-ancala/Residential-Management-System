<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Validates the incoming HTTP Basic Auth header against the
 * TOKEN_PASS1 (username) / TOKEN_PASS2 (password) credentials.
 *
 * Centralises the auth check that CarFX repeated inside every controller.
 */
class BasicTokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        if (! $authHeader || ! preg_match('/Basic\s+(.*)$/i', $authHeader, $matches)) {
            return response()->json([
                'Success' => false,
                'Message' => 'Missing Authorization header.',
            ], 401);
        }

        $decoded = base64_decode($matches[1]);
        if (! $decoded || ! str_contains($decoded, ':')) {
            return response()->json([
                'Success' => false,
                'Message' => 'Invalid Authorization header format.',
            ], 401);
        }

        [$username, $password] = explode(':', $decoded, 2);

        if ($username !== config('api.pass1') || $password !== config('api.pass2')) {
            Log::warning('Unauthorized API attempt', [
                'ip' => $request->ip(),
                'username_provided' => $username,
            ]);

            return response()->json([
                'Success' => false,
                'Message' => 'Invalid credentials.',
            ], 401);
        }

        return $next($request);
    }
}
