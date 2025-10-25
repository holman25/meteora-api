<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Verifica que la petición incluya una API Key válida.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $provided = $request->header('X-Api-Key');
        $expected = config('app.api_key');

        if (!$expected || !hash_equals((string) $expected, (string) $provided)) {
            return response()->json([
                'ok' => false,
                'error' => 'unauthorized',
                'message' => 'Acceso denegado. Falta o es incorrecta la API Key.'
            ], 401);
        }

        return $next($request);
    }
}
