<?php

include_once ('includes/defaults.php');
include_once ('includes/db.php');


$request = $_GET['request'];



if ($request === 'inventorydata') {
  $inventoryQuery = "SELECT phone_inventory.*, phone_models.*, phone_types.*, orgs.org_name, phones.phone_id
						FROM phone_inventory
							INNER JOIN phone_types
								ON phone_inventory_type_id = phone_type_id
							INNER JOIN phone_models
								ON phone_inventory_model_id = phone_model_id
							LEFT JOIN phones
								ON phone_inventory.phone_inventory_id = phones.phone_inventory_id
							LEFT JOIN orgs
								ON phones.phone_org_id = orgs.org_id
						WHERE phone_inventory.phone_inventory_tag LIKE :id;";

  $tag = $_GET['tag'];

  $xml = new SimpleXMLElement('<xml/>');

  $inventoryStmt = $pdo->prepare($inventoryQuery);
  $inventoryStmt->execute([$tag]);
  $inventory = $inventoryStmt->fetch(PDO::FETCH_ASSOC);

  $xml->addChild('phone_inventory_id', $inventory['phone_inventory_id']);
  $xml->addChild('phone_inventory_tag', $inventory['phone_inventory_tag']);
  $xml->addChild('phone_inventory_serial', $inventory['phone_inventory_serial']);
  $xml->addChild('phone_inventory_type_id', $inventory['phone_inventory_type_id']);
  $xml->addChild('phone_inventory_model_id', $inventory['phone_inventory_model_id']);
  $xml->addChild('phone_inventory_type_name', $inventory['phone_type_name']);
  $xml->addChild('phone_inventory_model_name', $inventory['phone_model_name']);
  $xml->addChild('phone_inventory_available', $inventory['phone_inventory_available']);
  $xml->addChild('org_name', $inventory['org_name']);

  OutputXML($xml);

} else if ($request === 'deployed') {
  $xml = new SimpleXMLElement('<xml/>');
  $id = $_GET['id'];

  $qry = "SELECT phone_is_deployed FROM phones WHERE phone_id = :id";
  $stmt = $pdo->prepare($qry);
  $stmt->execute(["id" => $id]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  $isDeployed = $row['phone_is_deployed'];

  //Not Deployed = 0, Is Deployed = 1, Returned = 2
  $isDeployed = ($isDeployed + 1) % 3;

  $qry = "UPDATE phones SET phone_is_deployed = :deployed WHERE phone_id = :id";
  $stmt = $pdo->prepare($qry);
  $stmt->execute(["deployed" => $isDeployed, "id" => $id]);


  $xml->addChild('phone_is_deployed', $isDeployed);
  $xml->addChild('phone_id', $id);

  OutputXML($xml);

} else if ($request === 'remove') {
  $xml = new SimpleXMLElement('<xml/>');
  $id = $_GET['id'];

  $qry = "SELECT phone_is_deployed, todelete_phone FROM phones WHERE phone_id = :id";
  $stmt = $pdo->prepare($qry);
  $stmt->execute(["id" => $id]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  $isDeployed = $row['phone_is_deployed'];
  $isToDelete = $row['todelete_phone'];

  if ($isDeployed == 0) {
    $isToDelete = $isToDelete == 1 ? 0 : 1;

    $qry = "UPDATE phones SET todelete_phone = :todelete WHERE phone_id = :id";
    $stmt = $pdo->prepare($qry);
    $stmt->execute(["todelete" => $isToDelete, "id" => $id]);
  }

  $xml->addChild('todelete_phone', $isToDelete);
  $xml->addChild('phone_is_deployed', $isDeployed);
  $xml->addChild('phone_id', $id);

  OutputXML($xml);
}









?>