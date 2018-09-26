<?php

namespace Binch\Controller;

use Binch\Entity\Group;
use Binch\Util\HttpError;
use Binch\Middleware\Auth;
use Binch\Util\Params;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Controller providing functions that handle group requests.
 *
 * @package Binch\Controller
 */
class GroupController extends Controller
{
    /**
     * Gets information about a specific group.
     *
     * @param Request $request The GET request
     * @param Response $response The current response
     * @param String $path The group path
     * @return Response The updated response
     */
    public function get(Request $request, Response $response, String $path)
    {
        $group = $request->getAttribute("group");
        return $response->withJson($group->export());
    }
    
    /**
     * Creates a new group.
     *
     * @param Request $request The POST request
     * @param Response $response The current response
     * @return Response The updated response
     * @throws HttpError If at least one parameter is not processable
     */
    public function post(Request $request, Response $response)
    {
        $params = new Params($request->getBody());
        $params->validate([
            "path" => ["required" => true, "type" => "string"],
            "name" => ["required" => true, "type" => "string"],
            "pass" => ["required" => false, "type" => "string"],
            "members" => ["required" => true, "array" => "string"]
        ]);
        
        $name = $params->name;
        $pass = $params->pass ?? "";
        
        $path = $params->path;
        $pathRegex = $this->container["settings"]["groupPathRegex"];
        if(!preg_match("/^$pathRegex$/", $path))
            throw new HttpError(422, "Group identifier can only contain letters, numbers and dashes");
        
        $members = array_filter($params->members);
        if(count($members) < 2)
            throw new HttpError(422, "Group must contain at least two members");
        if(count($members) !== count(array_unique($members)))
            throw new HttpError(422, "Group cannot have multiple members with the same name");
        
        if(Group::find($path))
            throw new HttpError(409, "Group identifier is already taken");
        
        $group = Group::create($path, $name, $members, $pass);
        $group->save();
        
        $url = $this->container->get("router")->pathFor("group", ["path" => $path]);
        return $response->withJson($group->export(true))->withRedirect($url, 201);
    }
    
    /**
     * Edits one or more properties of a specific group.
     *
     * @param Request $request The PATCH request
     * @param Response $response The current response
     * @param String $path The group path
     * @return Response The updated response
     * @throws HttpError If at least one parameter is not processable
     */
    public function patch(Request $request, Response $response, String $path)
    {
        $group = $request->getAttribute("group");
        
        $params = new Params($request->getBody());
        $params->validate([
            "name" => ["required" => false, "type" => "string"],
            "pass" => ["required" => false, "type" => "string"],
            "members" => ["required" => false, "array" => "string"]
        ]);
        
        if($params->name)
            $group->setName($params->name);
        
        if($params->pass)
            $group->setPass($params->pass);
        
        if($params->members)
        {
            $members = array_filter($params->members);
            if(count($members) !== count(array_unique($members)))
                throw new HttpError(422, "Group cannot have multiple members with the same name");
            
            if(!$group->setMembers($members))
                throw new HttpError(422, "Not enough names in array 'members'");
        }
        
        $group->save();
        
        return $response->withJson($group->export(true));
    }
    
    /**
     * Gets the authentication token.
     * This requires basic credentials that match the group password.
     *
     * @param Request $request The GET request
     * @param Response $response The current response
     * @param String $path The group path
     * @return Response The updated response
     * @throws HttpError If at least one parameter is not processable
     */
    public function getToken(Request $request, Response $response, String $path)
    {
        $group = $request->getAttribute("group");
        
        if(!$group->hasPassword())
            return $response->withStatus(204);
        
        $token = null;
        foreach(Auth::getCredentials($request, "Basic") as $pass)
            $token = $group->getToken($pass);
        
        if(!$token)
            throw new HttpError(403);
        
        return $response->withJson(["token" => $token]);
    }
}
