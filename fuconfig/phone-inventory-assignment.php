<?php
include_once ('FUConfig.php');

$currentRequest = new PageRequest($_REQUEST);

//Basic page validation -- org_id is required.
if (isset($currentRequest->org_id) == false) {
  $trace = debug_backtrace();
  trigger_error(
    'Org ID is required: ' .
    ' in ' . $trace[0]['file'] .
    ' on line ' . $trace[0]['line'],
    E_USER_ERROR
  );
}

$phoneInventoryList = new PhoneInventoryList();
$phoneInventoryList->LoadAvailablePhoneInventory();

//Loaded assigned phone list to compare location data.
$phoneList = new PhoneList();
$phoneList->LoadAllPhones();

$org = new Org();
$org->LoadFromDB($currentRequest->org_id);


?>



<?php include ('includes/header.php'); ?>

<script type="text/javascript">

  function doFilter() {
    $tagToFilter = $("#phone_inventory_tag").val().toUpperCase();

    $(".inventoryRow").each(function () {
      if ($tagToFilter.length > 0) {
        if ($(this).attr('id').toUpperCase() != $tagToFilter) {
          $(this).hide();
        } else {
          $(this).show();
        }
      } else {
        $(this).show();
      }
    });
  }

</script>

<div class="container-fluid">
  <div class="row mb-4">
    <div class="col">
      <h2 class="display-4"><b>Add Inventory Phone To: </b><?= $org->org_name ?></h2>
    </div>
  </div>

  <div class="row m-4">
    <div class="col-sm-auto">Filter By Inventory Tag: </div>
    <div class="col-sm-2">
      <input id="phone_inventory_tag" class="form-control">
    </div>
    <div class="col"><button class="btn btn-success" id="btnFilter" onClick="doFilter()">Filter</button></div>
  </div>

  <div class="row m-4 border-bottom border-dark">

  </div>

  <div class="row m-4">
    <div class="col">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">Tag #</th>
            <th scope="col">Location</th>
            <th scope="col">Type</th>
            <th scope="col">Model</th>
            <th scope="col">Serial</th>
            <th scope="col">Options</th>
          </tr>
        </thead>


        <?php

        $addToOrgRequest = new ButtonRequest();
        $addToOrgRequest->InitCreate("phone-inventory-assignment-crud.php", "Add To Org");
        $addToOrgRequest->SetVar("org_id", $currentRequest->org_id);

        foreach ($phoneInventoryList->GetList() as &$inventoryPhone) {
          $addToOrgRequest->SetVar("phone_inventory_id", $inventoryPhone->phone_inventory_id);

          $inventoryPhone->can_be_assigned = true;
          $matchingAssignedPhone = $phoneList->FindByInventoryId($inventoryPhone->phone_inventory_id);
          $orgName = "";

          if ($matchingAssignedPhone != null) {
            $inventoryPhone->can_be_assigned = false;
            $orgName = $matchingAssignedPhone->GetOrg()->org_name;
          }

          ?>

          <tr id="<?= $inventoryPhone->phone_inventory_tag ?>" class="inventoryRow">
            <th scope="row"><?= $inventoryPhone->phone_inventory_tag; ?></th>
            <td><?= $orgName; ?></td>
            <td><?= $inventoryPhone->GetPhoneType()->phone_type_name; ?></td>
            <td><?= $inventoryPhone->GetPhoneModel()->phone_model_name; ?></td>
            <td><?= $inventoryPhone->phone_inventory_serial; ?></td>
            <td>
              <?php if ($inventoryPhone->can_be_assigned) { ?>
                <?= $addToOrgRequest->GetAnchorButtonHTML(); ?>
              <?php } else { ?>
                Already Assigned
              <?php } ?>
            </td>
          </tr>


          <?php
        }

        ?>


    </div>
  </div>

</div>



<?php include ('includes/footer.php'); ?>