<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || $user->isAdmin != 1) {
            return response()->json([
                'message' => 'Unauthorized. Admins only.'
            ], 403);
        }

        return $next($request);
    }
}
