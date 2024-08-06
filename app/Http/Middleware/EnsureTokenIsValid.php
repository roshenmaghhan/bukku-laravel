<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\HttpStatus;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {   
        $token = $request->bearerToken();
        if ($token === null) {
            return response()->json([
                'message' => 'Unauthorized.'
            ], HttpStatus::UNAUTHORIZED->value);
        }

        return $next($request);
    }
}
