<?php

require __DIR__ . "/../vendor/autoload.php";

use Binch\Middleware\CORS;
use Binch\Util\HttpError;
use RedBeanPHP\R;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

$dotenv = Dotenv\Dotenv::create(__DIR__ . "/../");
$dotenv->safeLoad();

DEFINE("DEBUG", strcasecmp(getenv("APP_DEBUG"), "true") == 0);
error_reporting(DEBUG ? E_ALL : 0);

// init database
$host = getenv("DB_HOST");
$name = getenv("DB_NAME");

R::setup("mysql:host=$host;dbname=$name", getenv("DB_USER"), getenv("DB_PASS"));
R::setAutoResolve(true);

// init slim container
$container = new Container();

$container["settings"]["displayErrorDetails"] = DEBUG;
$container["settings"]["groupPathRegex"] = "[a-zA-Z0-9-]+";

$container["errorHandler"] = function($c)
{
    return function(Request $request, Response $response, Exception $exception) use ($c)
    {
        $response = CORS::withCORSHeaders($c["response"]);
        if($exception instanceof HttpError)
            return $response->withJson($exception, $exception->getStatus());
        else
            return $response->withJson(new HttpError(500, $exception->getMessage()), 500);
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
