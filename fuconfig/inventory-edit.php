<?php

include_once ('FUConfig.php');

$currentRequest = new PageRequest($_REQUEST);

$submitRequest = new PageRequest();
$submitRequest->SetSubmitPage("inventory-update.php");

//DataClass holding the inventory data.
$inventoryPhone = new PhoneInventory();

//Lists needed for dropdowns.
$phoneTypeList = new PhoneTypeList();
$phoneModelList = new PhoneModelList();

//We're either editing or adding new.
if ($currentRequest->IsUpdateRequest()) {
  $id = $currentRequest->GetID();

  //Load the inventory data from DB.
  $inventoryPhone->LoadFromDB($id);

  //Set the CRUD action type to Update (UPDATE)
  $submitRequest->SetUpdate($id);

} else if ($currentRequest->IsCreateRequest()) {

  //Create blank DataClass.
  $inventoryPhone->CreateEmpty();

  //Set the CRUD action type to Create (INSERT).
  $submitRequest->SetCreate();

} else {
  $trace = debug_backtrace();
  trigger_error(
    'Invalid Request State ' .
    ' in ' . $trace[0]['file'] .
    ' on line ' . $trace[0]['line'],
    E_USER_ERROR
  );
}



?>



<?php include ('includes/header.php'); ?>

<?php if ($currentRequest->IsReviewRequest()) { ?>
  <h2>Edit Inventory: <small class="text-muted"> <?= $inventoryPhone->phone_inventory_tag ?> </small></h2>
<?php } else { ?>
  <h2>Add Inventory</h2>
<?php } ?>

<form action="<?= $submitRequest->GetRequestAction() ?>" method="POST">
  <div class="container">
    <?php if ($currentRequest->IsReviewRequest()) { ?>
      <div class="row mt-4">
        <div class="col-1">ID: </div>
        <div class="col">
          <?= $inventoryPhone->phone_inventory_id ?>
          <input type="hidden" value="<?= $inventoryPhone->phone_inventory_id ?>" id="phone_inventory_id"
            name="phone_inventory_id" />
        </div>
      </div>
    <?php } ?>
    <div class="row mt-2">
      <div class="col-2">Tag: </div>
      <div class="col-3">
        <input type="text" class="form-control" value="<?= $inventoryPhone->phone_inventory_tag ?>"
          id="phone_inventory_tag" name="phone_inventory_tag" />
      </div>
    </div>

    <div class="row mt-2">
      <div class="col-2">Serial: </div>
      <div class="col-3">
        <input type="text" class="form-control" value="<?= $inventoryPhone->phone_inventory_serial ?>"
          id="phone_inventory_serial" name="phone_inventory_serial" />
      </div>
    </div>

    <div class="row mt-2">
      <div class="col-2">Type: </div>
      <div class="col-3">
        <select class="form-control" id="phone_inventory_type_id" name="phone_inventory_type_id">
          <?php
          foreach ($phoneTypeList->GetList() as $phoneType) {
            ?>
            <option <?= $phoneType->phone_type_id == $inventoryPhone->phone_inventory_type_id ? 'selected="selected"' : ''; ?>
              value="<?= $phoneType->phone_type_id; ?>">
              <?= $phoneType->phone_type_name; ?>
            </option>

            <?php
          }
          ?>

        </select>
      </div>
    </div>

    <div class="row mt-2">
      <div class="col-2">Model: </div>
      <div class="col-3">
        <select class="form-control" id="phone_inventory_model_id" name="phone_inventory_model_id">
          <?php
          foreach ($phoneModelList->GetList() as $phoneModel) {
            ?>
            <option <?= $phoneModel->phone_model_id == $inventoryPhone->phone_inventory_model_id ? 'selected="selected"' : ''; ?> value="<?= $phoneModel->phone_model_id; ?>">
              <?= $phoneModel->phone_model_name; ?>
            </option>


            <?php
          }
          ?>

        </select>
      </div>
    </div>

    <div class="row mt-2">
      <div class="col-2">Sip Username 1: </div>
      <div class="col-3">
        <input type="text" class="form-control" value="<?= $inventoryPhone->sip_username1 ?>" id="sip_username1"
          name="sip_username1" />
      </div>
    </div>

    <div class="row mt-2">
      <div class="col-2">Sip Password 1: </div>
      <div class="col-3">
        <input type="text" class="form-control" value="<?= $inventoryPhone->sip_password1 ?>" id="sip_password1"
          name="sip_password1" />
      </div>
    </div>

    <div class="row mt-2">
      <div class="col-2">Sip Username 2: </div>
      <div class="col-3">
        <input type="text" class="form-control" value="<?= $inventoryPhone->sip_username2 ?>" id="sip_username2"
          name="sip_username2" />
      </div>
    </div>

    <div class="row mt-2">
      <div class="col-2">Sip Password 2: </div>
      <div class="col-3">
        <input type="text" class="form-control" value="<?= $inventoryPhone->sip_password2 ?>" id="sip_password2"
          name="sip_password2" />
      </div>
    </div>

    <div class="row mt-5">
      <div class="col">
        <input type="hidden" id="phone_inventory_available" name="phone_inventory_available"
          value="<?= $inventoryPhone->phone_inventory_available ?>" />
        <input class="btn btn-success" type="submit" />
        <a class="btn btn-dark" href="inventory.php" role="button">Cancel</a>
      </div>
    </div>
  </div>
</form>


<?php include ('includes/footer.php'); ?>