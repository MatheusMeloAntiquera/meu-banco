<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Exceptions\InvalidDataException;

class ErrorHandleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        if (!empty($response->exception)) {
            if ($response->exception instanceof InvalidDataException) {
                return response()
                    ->json([
                        'success' => false,
                        "message" => $response->exception->getMessage(),
                        "errors" => $response->exception->getMessages()
                    ], $response->exception->getCode());
            } else {
                return response()
                    ->json(['success' => false, "message" => $response->exception->getMessage()], 500);
            }
        }
        return $response;
    }
}
