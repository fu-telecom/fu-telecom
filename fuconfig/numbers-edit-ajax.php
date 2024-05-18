<?php

include_once ('FUConfig.php');

$currentRequest = new PageRequest($_REQUEST);

$numberController = new NumberController();
$numberController->SetupFromPageRequest($currentRequest);
var_dump($numberController);
echo "<br><br>";
$numberController->ProcessRequest();

var_dump($numberController);
echo "<br><br>";

$xml = $numberController->AsXML();

var_dump($xml->asXML());
OutputXML($xml);

?>