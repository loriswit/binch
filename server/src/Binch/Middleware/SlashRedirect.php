<?php

namespace Binch\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Middleware that redirects paths with a trailing slash to the regular path.
 *
 * @package Binch\Middleware
 */
class SlashRedirect
{
    /**
     * If present, removes the trailing slash from the path and redirects GET requests.
     *
     * @param Request $request The request
     * @param Response $response The current response
     * @param callable $next The next middleware
     * @return Response The updated response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        
        if($path != "/" && substr($path, -1) == "/")
        {
            $uri = $uri->withPath(substr($path, 0, -1));
            
            if($request->getMethod() == "GET")
                return $response->withRedirect((string)$uri, 301);
            else
                return $next($request->withUri($uri), $response);
        }
        
        return $next($request, $response);
    }
}
