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
    include ('orgs-list.php');
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