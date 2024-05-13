<?php

include_once ('includes/defaults.php');
include_once ('includes/db.php');


//Data for Dropdowns
$typeQuery = "SELECT * FROM phone_types;";
$modelQuery = "SELECT * FROM phone_models;";
$inventoryTagsQuery = "SELECT phone_inventory.*, phone_models.*, phone_types.*, phones.phone_id, orgs.org_name FROM phone_inventory 
							 INNER JOIN phone_types 
								ON phone_inventory_type_id = phone_type_id
							 INNER JOIN phone_models
								ON phone_inventory_model_id = phone_model_id
							 LEFT JOIN phones
								ON phone_inventory.phone_inventory_id = phones.phone_inventory_id
							 LEFT JOIN orgs
								ON phones.phone_org_id = orgs.org_id
						WHERE phones.phone_id IS NULL AND phone_inventory_available = 1
						ORDER BY phone_inventory_tag;";

$phoneTypes = $pdo->query($typeQuery);
$phoneModels = $pdo->query($modelQuery);
$phoneInventoryTags = $pdo->query($inventoryTagsQuery);

if (isset($_GET["edit"])) {
  //Editing existing phone.

  $phoneDataQuery = "SELECT * FROM phones WHERE phone_id = :id";

  $numbersDataQuery = "SELECT numbers.* 
						FROM numbers
						INNER JOIN phone_number_assignment 
							ON numbers.number_id = phone_number_assignment.number_id
						WHERE phone_number_assignment.phone_id = :phone
						ORDER BY is_primary_number DESC";



} else {
  //Adding new phone.

  $phone['phone_inventory_type_id'] = 0;
  $phone['phone_inventory_model_id'] = 0;

}


?>



<?php include ('includes/header.php'); ?>





<div class="container-fluid">
  <div class="row mb-4">
    <div class="col">
      <?php if (isset($_GET['edit'])) { ?>
        <h2 class="display-4">Edit Phone: <small class="text-muted"> ??? </small></h2>
      <?php } else { ?>
        <h2 class="display-4">Add Phone</h2>
      <?php } ?>
    </div>
  </div>




  <div class="row mt-2 ml-4">
    <div class="col-1">Manually Enter Phone Data: </div>
    <div class="col-3"></div>

  </div>


  <div class="row mt-2 ml-4">
    <div class="col-1">Type: </div>
    <div class="col-3">
      <select class="form-control" id="inventory_type" name="inventory_type">
        <?php
        while ($row = $phoneTypes->fetch()) {
          ?>
          <option <?= $row['phone_type_id'] == $phone['phone_inventory_type_id'] ? 'selected="selected"' : ''; ?>
            value="<?= $row['phone_type_id']; ?>">
            <?= $row['phone_type_name']; ?>
          </option>

          <?php
        }
        ?>

      </select>
    </div>
  </div>

  <div class="row mt-2 ml-4">
    <div class="col-1">Model: </div>
    <div class="col-3">
      <select class="form-control" id="inventory_model" name="inventory_model">
        <?php
        while ($row = $phoneModels->fetch()) {
          ?>
          <option <?= $row['phone_model_id'] == $phone['phone_inventory_model_id'] ? 'selected="selected"' : ''; ?>
            value="<?= $row['phone_model_id']; ?>">
            <?= $row['phone_model_name']; ?>
          </option>


          <?php
        }
        ?>

      </select>
    </div>
  </div>


</div>



<?php include ('includes/footer.php'); ?>