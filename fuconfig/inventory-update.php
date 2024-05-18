<?php

include_once ('FUConfig.php');



$currentRequest = new PageRequest($_REQUEST);

$inventoryPhone = new PhoneInventory();
$inventoryPhone->LoadFromPageRequest($currentRequest);

if ($currentRequest->IsCreateRequest()) {

  $inventoryPhone->SaveToDB();

} else if ($currentRequest->IsUpdateRequest()) {

  $inventoryPhone->SaveToDB();

} else if ($currentRequest->IsDeleteRequest()) {

  $inventoryPhone->DeleteFromDB();

} else {
  $trace = debug_backtrace();
  trigger_error(
    'Invalid request ' .
    ' in ' . $trace[0]['file'] .
    ' on line ' . $trace[0]['line'],
    E_USER_ERROR
  );
  return null;
}

Redirect("/inventory.php");

?>