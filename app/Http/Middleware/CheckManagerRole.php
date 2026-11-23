<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckManagerRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post') && $request->header('X-Manager-Role') !== 'manager') {
            return response()->json([
                'status' => 'access_denied',
                'message' => 'Відхилено: операція доступна лише для ролі manager (заголовок X-Manager-Role).',
            ], 403);
        }

        $response = $next($request);

        $response->headers->set('X-Security-Policy', 'Enforced');

        return $response;
    }
}
