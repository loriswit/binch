<?php

require __DIR__ . "/../vendor/autoload.php";

use Binch\Util\HttpError;
use RedBeanPHP\R;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

define("CONFIG", json_decode(file_get_contents("../config.json"), true));

error_reporting(CONFIG["debug"] ? E_ALL : 0);

// init database
$db = CONFIG["database"];
$host = $db["host"];
$name = $db["name"];

R::setup("mysql:host=$host;dbname=$name", $db["user"], $db["pass"]);
R::setAutoResolve(true);

// init slim container
$container = new Container();

$container["settings"]["displayErrorDetails"] = CONFIG["debug"];
$container["settings"]["groupPathRegex"] = "[a-zA-Z0-9-]+";

$container["errorHandler"] = function($c)
{
    return function(Request $request, Response $response, Exception $exception) use ($c)
    {
        if($exception instanceof HttpError)
            return $c["response"]->withJson($exception, $exception->getStatus());
        else
            return $c["response"]->withJson(new HttpError(500, $exception->getMessage()), 500);
    };
};

$container["foundHandler"] = function()
{
    return new \Slim\Handlers\Strategies\RequestResponseArgs();
};

$container["notFoundHandler"] = function($c)
{
    return function(Request $request, Response $response) use ($c)
    {
        throw new HttpError(404);
    };
};

$container["notAllowedHandler"] = function($c)
{
    return function(Request $request, Response $response, array $methods) use ($c)
    {
        return $c["response"]
            ->withHeader("Allow", implode(", ", $methods))
            ->withJson(["error" => "Method Not Allowed"], 405);
    };
};

return $container;
