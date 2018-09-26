<?php

namespace Binch\Middleware;

use Binch\Util\HttpError;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Middleware ensuring that the user is authenticated and authorized.
 *
 * @package Binch\Middleware
 */
class Auth
{
    /**
     * Authenticates the user.
     *
     * @param Request $request The request
     * @param Response $response The current response
     * @param callable $next The next middleware
     * @return Response The updated response
     * @throws HttpError If the access is forbidden
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $group = $request->getAttribute("group");
        
        if(!$group->hasPassword())
            return $next($request, $response);
        
        foreach(self::getCredentials($request, "Bearer") as $token)
            if($group->authenticate($token))
                return $next($request, $response);
        
        throw new HttpError(403);
    }
    
    /**
     * Extracts the credentials from the request header.
     *
     * @param Request $request The request containing the credentials
     * @param String $type The credentials type (either "<i>Basic</i>" or "<i>Bearer</i>")
     * @return array A list of all the credentials found in the header
     * @throws HttpError If the request has no <i>Authorization</i> header
     */
    public static function getCredentials(Request $request, String $type)
    {
        if(!$request->hasHeader("Authorization"))
            throw new HttpError(401);
    
        $credentials = [];
        foreach($request->getHeader("Authorization") as $auth)
        {
            $args = explode(" ", $auth);
            if(count($args) != 2 || $args[0] != $type)
                continue;
            
            if($type == "Basic")
            {
                $cred = explode(":", base64_decode($args[1]));
                if(count($cred) != 2)
                    continue;
                
                $credentials[$cred[0]] = $cred[1];
            }
            else
                $credentials[] = $args[1];
        }
        
        return $credentials;
    }
}
