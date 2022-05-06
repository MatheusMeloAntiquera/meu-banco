<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Exceptions\InvalidDataException;
use App\Exceptions\WithHttpsCodeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        if (!empty($response->exception) && !$response->exception instanceof NotFoundHttpException) {
            if ($response->exception instanceof InvalidDataException) {
                return response()
                    ->json([
                        "success" => false,
                        "message" => $response->exception->getMessage(),
                        "errors" => $response->exception->getMessages()
                    ], $response->exception->getCode());
            }

            $statusCode =
                $response->exception instanceof WithHttpsCodeException
                ? $response->exception->getCode()
                : 500;
            return response()
                ->json(["success" => false, "message" => $response->exception->getMessage()], $statusCode);
        }
        return $response;
    }
}
