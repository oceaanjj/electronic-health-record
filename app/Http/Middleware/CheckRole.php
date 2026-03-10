<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        $user = $request->user();

        if (!$user || $user->role !== $role) {
            return response()->json([
                'message' => 'Forbidden. This endpoint requires the "' . $role . '" role.',
            ], 403);
        }

        return $next($request);
    }
}
