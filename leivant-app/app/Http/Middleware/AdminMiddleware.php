<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next, ?string $permission = null): Response
    {
        $user = $request->user();

        abort_unless($user?->is_admin, 403);

        if ($permission) {
            abort_unless($user->hasAdminPermission($permission), 403);
        }

        return $next($request);
    }
}

