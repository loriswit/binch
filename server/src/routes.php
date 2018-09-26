<?php

use Binch\Controller\GroupController;
use Binch\Controller\RoundController;
use Binch\Middleware\Auth;
use Binch\Middleware\GroupFetch;

$app->post("/groups", GroupController::class . ":post");

$pathRegex = $app->getContainer()["settings"]["groupPathRegex"];
$app->group("/group/{path:$pathRegex}", function()
{
    $this->get("", GroupController::class . ":get")->setName("group");
    $this->patch("", GroupController::class . ":patch")->add(new Auth());
    $this->get("/auth", GroupController::class . ":getToken");
    
    $this->get("/rounds", RoundController::class . ":get")->setName("rounds");
    $this->post("/rounds", RoundController::class . ":post")->add(new Auth());
})->add(new GroupFetch());
