<?php

include_once ('FUConfig.php');

$currentRequest = new PageRequest($_REQUEST);

$inventoryPhone = new PhoneInventory();
$inventoryPhone->LoadFromDB($currentRequest->phone_inventory_id);

if ($currentRequest->IsCreateRequest()) {
  $phone = $inventoryPhone->CreatePhoneFromInventory();
  $phone->phone_org_id = $currentRequest->org_id;

  $phone->SaveToDB();

  Redirect("/index.php");
}









?>