<?php

namespace Binch\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Middleware that enables cross-origin resource sharing.
 *
 * @package Binch\Middleware
 */
class CORS
{
    /**
     * Adds headers that allow CORS.
     *
     * @param Request $request The request
     * @param Response $response The current response
     * @param callable $next The next middleware
     * @return Response The updated response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $response = $next($request, $response);
        return $response
            ->withHeader("Access-Control-Allow-Origin", "*")
            ->withHeader("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, Accept, Origin, Authorization")
            ->withHeader("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, PATCH, OPTIONS");
    }
}
