<?php

namespace Binch\Controller;

use Binch\Entity\Consumer;
use Binch\Entity\Member;
use Binch\Entity\Round;
use Binch\Util\HttpError;
use Binch\Util\Params;
use RedBeanPHP\R;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Controller providing functions that handle round requests.
 *
 * @package Binch\Controller
 */
class RoundController extends Controller
{
    /**
     * Gets a list of all rounds in a specific group.
     *
     * @param Request $request The GET request
     * @param Response $response The current response
     * @param String $path The group path
     * @return Response The updated response
     */
    public function get(Request $request, Response $response, String $path)
    {
        $group = $request->getAttribute("group");
        
        $data = [];
        foreach(Round::findAll($group) as $round)
            $data[] = $round->export();
        
        return $response->withJson($data);
    }
    
    /**
     * Creates a new round in a specific group.
     *
     * @param Request $request The POST request
     * @param Response $response The current response
     * @param String $path The group path
     * @return Response The updated response
     * @throws HttpError If at least one parameter is not processable
     */
    public function post(Request $request, Response $response, String $path)
    {
        $group = $request->getAttribute("group");
        
        // avoid multiple rounds being created at the same time
        if(Round::find(R::isoDateTime(), $group))
            throw new HttpError(429);
        
        $params = new Params($request->getBody());
        $params->validate([
            "price" => ["required" => true, "type" => "int"],
            "payer" => ["required" => true, "type" => "string"],
            "consumers" => ["required" => true, "array" => "int"],
            "description" => ["required" => false, "type" => "string"]
        ]);
        
        $price = $params->price;
        if($price <= 0)
            throw new HttpError(422, "Price must be greater than zero");
        
        $payer = Member::find($params->payer, $group);
        if(!$payer)
            throw new HttpError(422, "Payer '" . $params->payer . "' does not exist");
        
        $consumers = [];
        foreach($params->consumers as $name => $amount)
        {
            if($amount == 0)
                continue;
            
            $member = Member::find($name, $group);
            if(!$member)
                throw new HttpError(422, "Consumer '$name' does not exist");
            if($amount < 0)
                throw new HttpError(422, "Consumer '$name' cannot drink a negative amount");
            
            $consumers[] = Consumer::create($member, $amount);
        }
        
        if(count($consumers) == 0)
            throw new HttpError(422, "Round must have at least one consumer");
        
        $description = $params->description ?? "";
        
        $round = Round::create($group, $price, $payer, $consumers, $description);
        $round->save();
        
        $url = $this->container->get("router")->pathFor("rounds", ["path" => $path]);
        return $response->withJson($round->export())->withRedirect($url, 201);
    }
    
    /**
     * Deletes or restores a specific round.
     *
     * @param Request $request The PATCH request
     * @param Response $response The current response
     * @param String $path The group path
     * @param String $date The round ISO date
     * @return Response The updated response
     * @throws HttpError If at least one parameter is not processable
     */
    public function patch(Request $request, Response $response, String $path, String $date)
    {
        $group = $request->getAttribute("group");
        $params = new Params($request->getBody());
        $params->validate([
            "deleted" => ["required" => true, "type" => "bool"]
        ]);
        
        $round = Round::find($date, $group);
        if($round == null)
            throw new HttpError(404);
        
        $round->setDeleted($params->deleted);
        $round->save();
        
        return $response->withJson($round->export());
    }
}
