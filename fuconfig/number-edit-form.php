<?php

include_once ('FUConfig.php');


$currentRequest = new PageRequest($_REQUEST);

$numberTypeList = NumberTypeList::LoadNumberTypeList();
$directoryList = PhoneDirectoryList::LoadPhoneDirectoryList();



if ($currentRequest->IsCreateRequest()) {
  $number = new Number();
  $number->CreateEmpty();

  $numberCount = 0;

  $assignment = new PhoneNumberAssignment();
  $assignment->CreateEmpty();

  $assignmentList = new PhoneNumberAssignmentList();
  $assignmentList->LoadByPhoneId($currentRequest->phone_id);
  //Set default display_order based on number of assigned numbers.
  $assignment->display_order = $assignmentList->GetCount() + 1;

} else if ($currentRequest->IsUpdateRequest()) {
  $number = new Number();
  $number->LoadFromDB($currentRequest->number_id);

  $assignmentList = new PhoneNumberAssignmentList();
  $assignmentList->LoadByNumberId($currentRequest->number_id);
  $numberCount = $assignmentList->GetCount();

  $assignment = new PhoneNumberAssignment();
  $assignment->LoadByPhoneAndNumber($currentRequest->phone_id, $currentRequest->number_id);

  //Can add max count comparison later.

} else {
  $trace = debug_backtrace();
  trigger_error(
    'Invalid Request Type' .
    ' in ' . $trace[0]['file'] .
    ' on line ' . $trace[0]['line'],
    E_USER_ERROR
  );
  return false;
}


?>


<form id="frmAddNumber">

  <input type="hidden" id="phone_id" name="phone_id" value="<?= $currentRequest->phone_id ?>" />
  <input type="hidden" id="number_id" name="number_id" value="<?= $currentRequest->number_id ?>" />

  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?= $currentRequest->IsUpdateRequest() ? 'Edit Number' : 'Add Number' ?></h5>
      </div>

      <div class="modal-body">
        <div class="row">
          <div class="col alert alert-danger m-2" id="frmAddNumberAlert"></div>
        </div>

        <?php if ($numberCount > 1) { ?>
          <div class="row">
            <div class="col alert alert-warning m-2"><strong>Warning: </strong>Multiple phones (<?= $numberCount ?>) have
              this number. All will be edited simultaneously.</div>
          </div>
        <?php } ?>

        <div class="row mt-2">
          <div class="col-3"><strong>Display Order: </strong></div>
          <div class="col-6"><input type="text" id="display_order" name="display_order" class="form-control"
              value="<?= $assignment->display_order ?>" /></div>
        </div>

        <div class="row mt-2">
          <div class="col-3"><strong>Caller ID: </strong></div>
          <div class="col-6"><input type="text" id="callerid" name="callerid" class="form-control"
              value="<?= $number->callerid ?>" /></div>
        </div>

        <div class="row mt-2">
          <div class="col-3"><strong>Number: </strong></div>
          <div class="col-6"><input type="text" id="number" name="number" class="form-control"
              value="<?= $number->number ?>" /></div>
        </div>

        <div class="row mt-2">
          <div class="col-3"><strong>Directory: </strong></div>
          <div class="col-6">
            <select class="form-control" id="directory_id" name="directory_id">
              <?php
              foreach ($directoryList->GetList() as $directory) {
                ?>
                <option <?= (($directory->default == 1 and $number->number_id == 0) or ($directory->directory_id == $number->directory_id)) ? 'selected="selected"' : ''; ?>
                  value="<?= $directory->directory_id; ?>">
                  <?= $directory->directory_name; ?>
                </option>
                <?php
              }
              ?>
            </select>
          </div>
        </div>

        <div class="row mt-2">
          <div class="col-3"><strong>Type: </strong></div>
          <div class="col-6">
            <select class="form-control" id="number_type_id" name="number_type_id">
              <?php
              foreach ($numberTypeList->GetList() as $numberType) {
                ?>
                <option <?= (($numberType->is_default == 1 and $number->number_id == 0) or ($numberType->number_type_id == $number->number_type_id)) ? 'selected="selected"' : ''; ?>
                  value="<?= $numberType->number_type_id; ?>">
                  <?= $numberType->number_type_name; ?>
                </option>
                <?php
              }
              ?>
            </select>
          </div>
        </div>

        <div class="row mt-2">
          <div class="col-3"><strong>Password Index: </strong></div>
          <div class="col-6">
            <select class="form-control" id="password_index" name="password_index">
              <option value="1" <?php if ($number->password_index == 1) { ?> selected="selected" <?php } ?>>User/Pass 1
              </option>
              <option value="2" <?php if ($number->password_index == 2) { ?> selected="selected" <?php } ?>>User/Pass 2
              </option>
            </select>
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button id="btnSubmitNumberAddEditForm"
          class="btn btn-primary"><?= $currentRequest->IsUpdateRequest() ? 'Edit' : 'Add' ?></button>
        <button id="btnCancelNumberAddEditForm" class="btn btn-dark" data-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>


</form>