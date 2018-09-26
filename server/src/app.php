<?php

use Binch\Middleware\CORS;
use Binch\Middleware\SlashRedirect;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$container = require __DIR__ . "/config.php";
$app = new \Slim\App($container);

// redirect paths with a trailing slash
$app->add(new SlashRedirect());

// enable CORS
$app->add(new CORS());
$app->options("/{routes:.+}", function(Request $request, Response $response)
{
    return $response;
});

// define routes
require __DIR__ . "/routes.php";

// treat remaining routes as "not found"
$app->map(["GET", "POST", "PUT", "DELETE", "PATCH"], "/{routes:.+}",
    function(Request $request, Response $response)
    {
        $handler = $this->notFoundHandler;
        return $handler($request, $response);
    });

$app->run();
