<?php

include_once ('FUConfig.php');

$currentRequest = new PageRequest($_REQUEST);

$formSubmitRequest = new PageRequest();
$formSubmitRequest->SetSubmitPage("orgs-update.php");

//This Org, default to blank.
$org = new Org();

if ($currentRequest->IsUpdateRequest()) {
  //Load data for edit.
  $org->LoadFromDB($currentRequest->GetID());

  //Set request type.
  $formSubmitRequest->SetRequestType(PageRequest::UPDATE);

  //REQUIRED: Set ID field for form submition. 
  $formSubmitRequest->SetID($currentRequest->GetID());

} else if ($currentRequest->IsCreateRequest()) {
  //Blank data for add is default in new Org

  //Set request type.
  $formSubmitRequest->SetRequestType(PageRequest::CREATE);

} else {
  $trace = debug_backtrace();
  trigger_error(
    'Invalid Request Type:' .
    ' in ' . $trace[0]['file'] .
    ' on line ' . $trace[0]['line'],
    E_USER_ERROR
  );

}


?>



<?php include ('includes/header.php'); ?>



<div class="container-fluid">
  <div class="row mb-4">
    <div class="col">
      <?php if ($currentRequest->IsUpdateRequest()) { ?>
        <h2 class="display-4">Edit Org: <small class="text-muted"><?= $org->org_name ?> </small></h2>
      <?php } else { ?>
        <h2 class="display-4">Add Org</h2>
      <?php } ?>
    </div>
  </div>

  <form method="post" action="<?= $formSubmitRequest->GetRequestAction() ?>">

    <input type="hidden" name="id" value="<?= $org->org_id ?>" />

    <div class="row m-2 ml-4">
      <div class="col-2"><strong>Name: </strong></div>
      <div class="col-3"><input class="form-control" type="text" id="name" name="org_name"
          value="<?= $org->org_name ?>" /></div>
    </div>

    <div class="row m-2 ml-4">
      <div class="col-2"><strong>Contact Name: </strong></div>
      <div class="col-3"><input class="form-control" type="text" id="contactname" name="org_contactname"
          value="<?= $org->org_contactname ?>" /></div>
    </div>

    <div class="row m-2 ml-4">
      <div class="col-2"><strong>Contact Phone: </strong></div>
      <div class="col-3"><input class="form-control" type="text" id="contactphone" name="org_contactphone"
          value="<?= $org->org_contactphone ?>" /></div>
    </div>

    <div class="row m-2 ml-4">
      <div class="col-2"><strong>Contact Email: </strong></div>
      <div class="col-3"><input class="form-control" type="text" id="contactemail" name="org_contactemail"
          value="<?= $org->org_contactemail ?>" /></div>
    </div>

    <div class="row m-4">
      <div class="col-4">
        <input type="submit" class="btn btn-primary" />
        <a class="btn btn-dark" href="index.php" role="button">Cancel</a>
      </div>
    </div>

  </form>

</div>

<?php include ('includes/footer.php'); ?>