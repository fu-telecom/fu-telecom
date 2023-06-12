<?php 

//include_once('includes/defaults.php');
//include_once('includes/db.php'); 

include_once('FUConfig.php');



$currentRequest = new PageRequest($_REQUEST);

$inventoryPhone = new PhoneInventory();
$inventoryPhone->LoadFromPageRequest($currentRequest);

if ($currentRequest->IsCreateRequest()) {
	
	//echo "Create Request: Calling SaveToDB() <br /><br /><br />";
	$inventoryPhone->SaveToDB();
	//echo "Finished Saving.<br /><br/>";
	
} else if ($currentRequest->IsUpdateRequest()) {
	
	//echo "Update Request: Calling SaveToDB() <br /><br /><br />";
	$inventoryPhone->SaveToDB();
	//echo "Finished Saving.<br /><br/>";
	
} else if ($currentRequest->IsDeleteRequest()) {
	
	$inventoryPhone->DeleteFromDB();
	
} else {
	$trace = debug_backtrace();
	trigger_error(
		'Invalid request ' . 
		' in ' . $trace[0]['file'] .
		' on line ' . $trace[0]['line'],
		E_USER_ERROR);
	return null;
}

Redirect("/inventory.php");




/*
// 1 = update
// 2 = add
// 3 = delete
// else = update available status
$updateType = $_GET['update'];


//If this is a POST, we're doing updates.
//Otherwise it's a view.
if ($updateType == 1) {
	//Edit
	var_dump($_POST);
	
	$phone_inventory_id = $_POST['inventory_id'];
	$phone_inventory_tag = $_POST['inventory_tag'];
	$phone_inventory_serial = $_POST['inventory_serial'];
	$phone_inventory_type_id = $_POST['inventory_type'];
	$phone_inventory_model_id = $_POST['inventory_model'];
	
	
	$updateQuery = "UPDATE phone_inventory SET 
						phone_inventory_tag = :tag, 
						phone_inventory_serial = :serial, 
						phone_inventory_type_id = :type, 
						phone_inventory_model_id = :model
					WHERE phone_inventory_id = :id;";
					
	$update = $pdo->prepare($updateQuery);
	$update->execute(['id' => $phone_inventory_id, 'tag' => $phone_inventory_tag, 'serial' => $phone_inventory_serial, 
					'type' => $phone_inventory_type_id, 'model' => $phone_inventory_model_id]);
	
	Redirect("/inventory.php");
	
} else if ($updateType == 2) {
	//Add

	$phone_inventory_tag = $_POST['inventory_tag'];
	$phone_inventory_serial = $_POST['inventory_serial'];
	$phone_inventory_type_id = $_POST['inventory_type'];
	$phone_inventory_model_id = $_POST['inventory_model'];
	
	$addQuery = "INSERT INTO phone_inventory (phone_inventory_tag, phone_inventory_serial, 
							phone_inventory_type_id, phone_inventory_model_id) 
					VALUES(:tag, :serial, :type, :model);";
	
	$update = $pdo->prepare($addQuery);
	$update->execute(['tag' => $phone_inventory_tag, 'serial' => $phone_inventory_serial, 
					'type' => $phone_inventory_type_id, 'model' => $phone_inventory_model_id]);
	
	Redirect("/inventory.php");
	
} else if ($updateType == 3) {
	
	$phone_inventory_id = $_GET['id'];
	
	$deleteQuery = "DELETE FROM phone_inventory WHERE phone_inventory_id = :id";
	
	$update = $pdo->prepare($deleteQuery);
	$update->execute(['id' => $phone_inventory_id]);
	
	Redirect("/inventory.php");
	
} else {


	$phone_inventory_id = $_GET["id"];
	$phone_inventory_available = $_GET["available"];

	$updateQuery = "UPDATE phone_inventory 
					SET phone_inventory_available = :available 
					WHERE phone_inventory_id = :id";
					
	$update = $pdo->prepare($updateQuery);
	$update->execute(['id' => $phone_inventory_id, 'available' => $phone_inventory_available]);

	Redirect("/inventory.php");

}*/


?>