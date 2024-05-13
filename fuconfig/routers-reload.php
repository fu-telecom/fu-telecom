<?php
include_once ('FUConfig.php');

$currentRequest = new PageRequest($_REQUEST);

$router = new Router();
$router->LoadFromDB($currentRequest->router_id);

$routerHandler = new RouterHandler();
$routerHandler->CheckAndUpdateRouter($router);

OutputXML($routerHandler->AsXML());

?>