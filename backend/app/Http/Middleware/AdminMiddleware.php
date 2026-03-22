<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Требуется авторизация'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        if ($request->user()->role_id != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен. Требуются права администратора.'
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }

        return $next($request);
    }
}
