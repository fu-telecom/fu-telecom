<?php

include_once ('FUConfig.php');

$currentRequest = new PageRequest($_REQUEST);

$routerController = new RouterController();
$routerController->ProcessRequest($currentRequest);

OutputXML($routerController->AsXML());

?>