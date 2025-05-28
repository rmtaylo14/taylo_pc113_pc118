<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Token;

class AccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $api = $request->header('x-api-key');
        $origin = $request->header('origin');
        $token = Token::where('token', $api)->first();

        if(empty($api)){
            return response()->json([
                'message' => 'forbidden',
            ], 403);
        }

        if(empty($origin) || $origin != $token->origin) {
            return response()->json([
                'message' => 'forbidden',
            ], 403);
        }

        if($token){
            return $next($request);
        }else {
            return response()->json([
                'message' => 'forbidden'
            ], 403);
        }
    }
}
