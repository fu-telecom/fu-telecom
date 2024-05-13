<?php
include_once ('FUConfig.php');

// ButtonRequest CRUD request handler, set to CREATE.
$addNewOrgButton = new ButtonRequest();
$addNewOrgButton->InitCreate("orgs-edit.php", "Add New Organization");

$editOrgButton = new ButtonRequest();
$editOrgButton->InitUpdate("orgs-edit.php", "Edit");
$editOrgButton->AddCSSClass("float-right");
$editOrgButton->AddCSSClass("m-1");

$deleteOrgButton = new ButtonRequest();
$deleteOrgButton->InitDelete("orgs-update.php", "Delete");
$deleteOrgButton->SetOnClick("return confirm('Are you sure you want to delete this org?');");
$deleteOrgButton->AddCSSClass("float-right");
$deleteOrgButton->AddCSSClass("m-1");

$orgList = new OrgList();
$orgList->LoadAllOrgs();

$phoneNumberAssignmentList = new PhoneNumberAssignmentList();
$phoneNumberAssignmentList->LoadAll();
?>

<?php include ('includes/header.php'); ?>

<div class="container-fluid">
  <div class="row">
    <div class="col">
      <h1 class="display-4">Master List</h1>
    </div>
    <div class="col-sm align-self-center justify-content-end">
      <?= $addNewOrgButton->GetAnchorButtonHTML() ?>
    </div>
  </div>
  <?php
  foreach ($orgList->GetList() as $currentOrg) {
    $phoneList = new PhoneList();
    $phoneList->LoadOrgPhones($currentOrg->org_id);
    $editOrgButton->SetID($currentOrg->org_id);
    $deleteOrgButton->SetID($currentOrg->org_id);
    ?>
    <div class="row mx-2">
      <div class="col">
        <div class="row group-header">
          <div class="col-sm-3">
            <h4><?= $currentOrg->org_name ?></h4>
          </div>
          <div class="col-sm-auto">
            <strong>Contact Name: </strong><?= $currentOrg->org_contactname ?>
          </div>
          <div class="col-sm-auto">
            <strong>Email: </strong><?= $currentOrg->org_contactemail ?>
          </div>
          <div class="col-sm-auto">
            <strong>Phone: </strong><?= $currentOrg->org_contactphone ?>
          </div>
          <div class="col"><?= $editOrgButton->GetAnchorButtonHTML() ?>
            <?php if ($phoneList->GetCount() == 0) { ?>
              <?= $deleteOrgButton->GetAnchorButtonHTML() ?>
              <?php
            } ?>
            <a class="btn btn-success float-right m-1"
              href="phone-inventory-assignment.php?org_id=<?= $currentOrg->org_id ?>" role="button">Add Inventory
              Phone</a>
            <a class="btn btn-success float-right m-1"
              href="router-inventory-assignment.php?org_id=<?= $currentOrg->org_id ?>" role="button">Add Router</a>
          </div>
        </div>
        <div class="row">
          <div class="col-auto">
            <h3>Phone List:</h3>
          </div>
        </div>
        <?php
        if ($phoneList->GetCount() > 0) {
          foreach ($phoneList->GetList() as $phone) { ?>
            <div class="row bg-secondary rounded text-light ml-4 mt-1">
              <div class="col-auto">
                <?php
                if ($phone->phone_is_deployed == 0) { ?>
                  <button id="btnDeployed<?= $phone->phone_id ?>" onClick="updateDeployed(<?= $phone->phone_id ?>)"
                    class="btn btn-warning p-4 m-1 float-right">Not Deployed</button>
                  <?php
                } else if ($phone->phone_is_deployed == 1) { ?>
                    <button id="btnDeployed<?= $phone->phone_id ?>" onClick="updateDeployed(<?= $phone->phone_id ?>)"
                      class="btn btn-danger p-4 m-1 float-right">Deployed</button>
                  <?php
                } else { ?>
                    <button id="btnDeployed<?= $phone->phone_id ?>" onClick="updateDeployed(<?= $phone->phone_id ?>)"
                      class="btn btn-success p-4 m-1 float-right">Returned</button>
                  <?php
                } ?>
              </div>
              <div class="col">
                <div class="row align-items-center ">
                  <div class="col-auto">
                    <strong>Tag: </strong><?= $phone->GetPhoneInventory()->phone_inventory_tag ?>
                  </div>
                  <div class="col-auto">
                    <strong>Type: </strong><?= $phone->GetPhoneType()->phone_type_name ?>
                  </div>
                  <div class="col-auto">
                    <strong>Model: </strong><?= $phone->GetPhoneModel()->phone_model_name ?>
                  </div>
                  <div class="col-auto">
                    <strong>Serial: </strong><?= $phone->phone_serial ?>
                  </div>
                  <div class="col">
                    <button id="btnAddExistingNumber" onClick="addExistingNumber(<?= $phone->phone_id ?>)"
                      class="btn btn-primary p-1 m-1 float-right">Add Existing Number</button>
                    <button id="btnAddNewNumber" onClick="addNewNumber(<?= $phone->phone_id ?>)"
                      class="btn btn-primary p-1 m-1 float-right">Add New Number</button>
                  </div>
                </div>
                <?php if ($phone->todelete_phone == 0) {
                  $btnDeleteClass = "btn-warning";
                  $btnDeleteLabel = "Remove Phone";
                  $deleteNotification = "";
                } else {
                  $btnDeleteClass = "btn-danger";
                  $btnDeleteLabel = "Removed";
                  $deleteNotification = "----------- Phone Marked For Deletion!!! -----------";
                } ?>
                <div class="row">
                  <div class="col-auto">
                    <strong>Max Numbers: </strong><?= $phone->GetPhoneModel()->phone_model_max_numbers ?>
                  </div>
                  <div class="col-auto" id="phoneNotification<?= $phone->phone_id ?>"><?= $deleteNotification ?></div>
                  <div class="col">
                    <?php if ($phone->phone_type_id == PhoneType::SCCP) { ?>
                      <button id="btnReloadPhone" onClick="reloadSCCPPhone(<?= $phone->phone_id ?>)" class="btn btn-info">Reload
                        SCCP Phone</button>
                      <button id="btnReloadPhone" onClick="restartSCCPPhone(<?= $phone->phone_id ?>)"
                        class="btn btn-info">Restart
                        SCCP Phone</button>
                      <button id="btnRedoPhone" onClick="redoSCCPPhone(<?= $phone->phone_id ?>)" class="btn btn-info">Redo In
                        Asterisk</button>
                    <?php } ?>
                    <button id="btnDeletePhone<?= $phone->phone_id ?>" onClick="removePhone(<?= $phone->phone_id ?>)"
                      class="btn <?= $btnDeleteClass ?> p-1 m-1 float-right"><?= $btnDeleteLabel ?>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="row mb-4 ml-4 mt-1" id="numberList<?= $phone->phone_id ?>">
              <?php include ('numbers-list.php'); ?>
            </div>
            <?php
          }
        } else { ?>
          <div class="row my-2 mx-4">
            <div class="col alert-info mb-4 p-1 rounded"><strong>No Phones Added</strong></div>
          </div>
          <?php
        } ?>
        <div class="row">
          <div class="col-auto">
            <h3>Router List:</h3>
          </div>
        </div>
        <?php include ('routers-list.php'); ?>
        <div class="row mt-2 mb-4 justify-content-end">
          <div class="col">
            &nbsp;
          </div>
          <div class="col">
            &nbsp;
          </div>
          <div class="col">
            &nbsp;
          </div>
          <div class="col">
            &nbsp;
          </div>
        </div>
      </div>
    </div>
    <?php
  } ?>
</div>

<div class="modal fade" id="addNumberModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
</div>
<div class="modal fade" id="editRouterModal" tabindex="-1" role="dialog" aria-labelledby="routerEditTitle"
  aria-hidden="true">
</div>
<div class="modal fade" id="routerUpdateModal" tabindex="-1" role="dialog" aria-labelledby="routerUpdateTitle"
  aria-hidden="true">
  <?php include ('router-reload-modal.php'); ?>
</div>
<div class="modal fade" id="processPhonesModal" tabindex="-1" role="dialog" aria-labelledby="processingModalTitle"
  aria-hidden="true">
  <?php include ('phone-processing-modal.php'); ?>
</div>
<?php include ('includes/footer.php'); ?>