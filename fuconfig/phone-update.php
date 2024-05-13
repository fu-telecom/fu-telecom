<?php





/*
include_once('includes/defaults.php');
include_once('includes/db.php'); 



//This is being handled in phone-inventory-assignment-crud.php for now. 

if (isset($_GET['add'])) {
  $addQuery = "INSERT INTO phones (phone_type_id, phone_model_id, phone_serial, phone_org_id, 
            phone_is_inventory, phone_inventory_id, altered, added) 
        VALUES (:type, :model, :serial, :org, :is_inventory, :inventory, 1, 1);";
        
  $type = $_POST['type_id'];
  $model = $_POST['model_id'];
  $serial = $_POST['serial'];
  $org = $_POST['org_id'];
  $inventory = $_POST['inventory_id'];
  $is_inventory = $_POST['is_inventory'];

        
  $addStmt = $pdo->prepare($addQuery);
  $addStmt->execute(['type' => $type, 'model' => $model, 'serial' => $serial, 'org' => $org, 
    'inventory' => $inventory, 'is_inventory' => $is_inventory]);
  
  Redirect("/index.php");
  
} else if (isset($_GET['edit'])) {
  
  
}*/











?>