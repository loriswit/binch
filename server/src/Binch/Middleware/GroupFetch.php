<?php

namespace Binch\Middleware;

use Binch\Entity\Group;
use Binch\Util\HttpError;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Middleware that fetches the properties of the group so that they can be accessed in controllers functions.
 *
 * @package Binch\Middleware
 */
class GroupFetch
{
    /**
     * Fetches the group properties.
     *
     * @param Request $request The request
     * @param Response $response The current response
     * @param callable $next The next middleware
     * @return Response The updated response
     * @throws HttpError If the group does not exist
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $path = $request->getAttribute("route")->getArgument("path");
        $group = Group::find($path);
        if($group == null)
            throw new HttpError(404);
        
        return $next($request->withAttribute("group", $group), $response);
    }
}
