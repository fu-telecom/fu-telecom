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


$routerInventoryList = new RouterList();
$routerInventoryList->LoadAll();

$org = new Org();
$org->LoadFromDB($currentRequest->org_id);


?>



<?php include ('includes/header.php'); ?>

<script type="text/javascript">

  function doFilter() {
    $tagToFilter = $("#router_number").val().toUpperCase();

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
      <h2 class="display-4"><b>Add Router To: </b><?= $org->org_name ?></h2>
    </div>
  </div>

  <div class="row m-4">
    <div class="col-sm-auto">Filter By Router Number: </div>
    <div class="col-sm-2">
      <input id="router_number" class="form-control">
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
            <th scope="col">Number</th>
            <th scope="col">Location</th>
            <th scope="col">Chan 2.4/5</th>
            <th scope="col">Enclosed</th>
            <th scope="col">Notes</th>
            <th scope="col">Options</th>
          </tr>
        </thead>


        <?php

        $addToOrgRequest = new ButtonRequest();
        $addToOrgRequest->InitUpdate("router-inventory-assignment-crud.php", "Add To Org");
        $addToOrgRequest->SetVar("org_id", $currentRequest->org_id);
        $addToOrgRequest->SetID($currentRequest->org_id);

        foreach ($routerInventoryList->GetList() as &$router) {
          $addToOrgRequest->SetVar("router_id", $router->router_id);

          $assignedOrg = $router->GetOrg();

          $router->can_be_assigned = true; //Marker for use later in this file only.
          $orgName = "";

          if ($assignedOrg != null) {
            $router->can_be_assigned = false;
            $orgName = $assignedOrg->org_name;
          }

          if ($router->available == 0)
            $router->can_be_assigned = false;

          ?>

          <tr id="<?= $router->number ?>" class="inventoryRow">
            <th scope="row"><?= $router->number; ?></th>
            <td><?= $orgName; ?></td>
            <td><?= $router->channel_24; ?>/<?= $router->channel_5; ?></td>
            <td><?= $router->enclosed; ?></td>
            <td><?= $router->notes; ?></td>
            <td>
              <?php
              if ($router->available == 0) {
                echo "Unavailable";
              } else if ($router->can_be_assigned == true) { ?>
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