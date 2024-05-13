<?php

include_once ('FUConfig.php');

$currentRequest = new PageRequest($_REQUEST);

$router = new Router();
$router->LoadFromDB($currentRequest->router_id);

if ($currentRequest->IsUpdateRequest()) {
  $router->org_id = $currentRequest->org_id;
  $router->SaveToDB();

  Redirect("/index.php");
}









?>