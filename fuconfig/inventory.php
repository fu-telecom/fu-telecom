<?php

include_once ('FUConfig.php');

$phoneInventoryList = new PhoneInventoryList();
$phoneInventoryList->LoadAllPhoneInventory();

//Loaded assigned phone list to compare location data.
$phoneList = new PhoneList();
$phoneList->LoadAllPhones();

$addRequest = new PageRequest();
$addRequest->SetSubmitPage("inventory-edit.php");
$addRequest->SetCreate();

?>



<?php include ('includes/header.php'); ?>

<script type="text/javascript">
  function sureDelete(idToDelete, tag) {
    if (confirm("Are you sure you want to delete " + tag + "?")) {
      $("#btnDelete" + idToDelete).attr("href", "inventory-update.php?update=3&id=" + idToDelete);
    }
    else {
      return false;
    }
  }
</script>



<div class="container-fluid">
  <div class="row">
    <div class="col">
      <h1 class="display-4">Inventory</h1>
    </div>
    <div class="col-sm align-self-center justify-content-end"><a class="btn btn-primary"
        href="<?= $addRequest->GetRequestAction() ?>" role="button">Add New Phone</a></div>
  </div>
  <div class="row">
    <div class="col-sm">

      <table class="table">
        <thead>
          <tr>
            <th scope="col">Tag #</th>
            <th scope="col">Status</th>
            <th scope="col">Location</th>
            <th scope="col">Type</th>
            <th scope="col">Model</th>
            <th scope="col">Serial</th>
            <th scope="col">Options</th>
          </tr>
        </thead>

        <tbody>

          <?php
          //Edit Button
          $reviewRequest = new ButtonRequest();
          $reviewRequest->Init(PageRequest::UPDATE, ButtonRequest::PRIMARY, ButtonRequest::BTN, "inventory-edit.php", "Edit");

          //Delete Button
          $deleteRequest = new ButtonRequest();
          $deleteRequest->Init(PageRequest::DELETE, ButtonRequest::DANGER, ButtonRequest::BTN, "inventory-update.php", "Delete");
          $deleteRequest->SetOnClick("return confirm('Are you sure you want to delete this item?');");

          //Available Button
          //This ButtonRequest generates the HTML for the Bootstrap anchor button.
          $updateAvailableButton = new ButtonRequest();
          $updateAvailableButton->Init(PageRequest::UPDATE, ButtonRequest::PRIMARY, ButtonRequest::BTN);

          foreach ($phoneInventoryList->GetList() as &$inventoryPhone) {
            $orgName = "";

            //Check to see if phone is assigned somewhere.
            $matchingAssignedPhone = $phoneList->FindByInventoryId($inventoryPhone->phone_inventory_id);
            //If it's assigned, get the assigned org name.
            if ($matchingAssignedPhone != null) {
              $orgName = $matchingAssignedPhone->GetOrg()->org_name;
            }

            //Set the ID field for buttons.
            $reviewRequest->SetID($inventoryPhone->phone_inventory_id);
            $deleteRequest->SetID($inventoryPhone->phone_inventory_id);

            //Hidden form to update available flag on the inventory page.
            $updateAvailableForm = new FormRequest(
              "update_available_form",
              PageRequest::UPDATE,
              "inventory-update.php",
              FormRequest::POST
            );
            $updateAvailableForm->SetID($inventoryPhone->phone_inventory_id);

            //Data for form.
            $updateAvailableData = clone $inventoryPhone;


            //If this phone is set to available, this request can turn it to unavailable or vice-versa.
            if ($inventoryPhone->phone_inventory_available == 1) {
              //Is available, set to unavailable if processed.
              $updateAvailableData->phone_inventory_available = 0;
              $updateAvailableButton->SetDisplayColor(ButtonRequest::SUCCESS);
              $updateAvailableButton->SetButtonLabel("Available");
            } else {
              //Is unavailable, set to available if processed.
              $updateAvailableData->phone_inventory_available = 1;
              $updateAvailableButton->SetDisplayColor(ButtonRequest::DANGER);
              $updateAvailableButton->SetButtonLabel("Not Available");
            }
            ?>

            <tr>
              <th scope="row"><?= $inventoryPhone->phone_inventory_tag; ?></th>
              <td>
                <?= $updateAvailableForm->OutputDataClassAsHiddenForm(
                  $updateAvailableData,
                  $updateAvailableButton->GetSubmitButton()
                ); ?>
              </td>
              <td><?= $orgName; ?></td>
              <td><?= $inventoryPhone->GetPhoneType()->phone_type_name; ?></td>
              <td><?= $inventoryPhone->GetPhoneModel()->phone_model_name; ?></td>
              <td><?= $inventoryPhone->phone_inventory_serial; ?></td>
              <td>
                <?= $reviewRequest->GetAnchorButtonHTML(); ?>
                <?= $deleteRequest->GetAnchorButtonHTML(); ?>
              </td>
            </tr>

          <?php } ?>
        </tbody>

      </table>

    </div>
  </div>
</div>



<?php include ('includes/footer.php'); ?>