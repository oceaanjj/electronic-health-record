<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        $user = $request->user();
        $userRole = strtolower((string) optional($user)->role);
        $requiredRole = strtolower($role);

        if (!$user || $userRole !== $requiredRole) {
            return response()->json([
                'message' => 'Forbidden. This endpoint requires the "' . $requiredRole . '" role.',
            ], 403);
        }

        return $next($request);
    }
}
