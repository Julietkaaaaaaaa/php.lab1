<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (
            in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)
            && ! in_array($request->header('X-User-Role'), $roles, true)
        ) {
            return response()->json([
                'status' => 'access_denied',
                'message' => 'Відхилено: потрібна роль '.implode(' або ', $roles).' (заголовок X-User-Role).',
            ], 403);
        }

        $response = $next($request);

        $response->headers->set('X-Security-Policy', 'Enforced');

        return $response;
    }
}
