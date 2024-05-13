<?php

include_once ('includes/defaults.php');
include_once ('includes/db.php');

$numberTypesQuery = "SELECT * FROM number_types;";
$numberTypesStmt = $pdo->prepare($numberTypesQuery);
$numberTypesStmt->execute();

$phone_id = $_GET['phone_id'];

?>


<form id="frmAddExistingNumber">

  <input type="hidden" id="phone_id" name="phone_id" value="<?= $phone_id ?>" />


  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Existing Number</h5>
      </div>

      <div class="modal-body">
        <div class="row">
          <div class="col alert alert-danger m-2" id="frmAddNumberAlert"></div>
        </div>

        <div class="row mt-2">
          <div class="col-3"><strong>Number: </strong></div>
          <div class="col-6">
            <select class="form-control" id="number_id" name="number_id">

            </select>
          </div>
        </div>

        <div class="row mt-2">
          <div class="col-3"><strong>Type: </strong></div>
          <div class="col-6">
            <select class="form-control" id="number_type_id" name="number_type_id" onClick="updateExistingNumberList()">
              <?php
              while ($row = $numberTypesStmt->fetch()) {
                ?>
                <option <?= $row['number_type_id'] == 2 ? 'selected="selected"' : ''; ?> value="<?= $row['number_type_id']; ?>">
                  <?= $row['number_type_name']; ?>
                </option>
                <?php
              }
              ?>
            </select>
          </div>
        </div>

        <div class="row mt-2">
          <div class="col-3"><strong>Sort by: </strong></div>
          <div class="col-6">
            <div class="form-check">
              <input type="radio" class="form-check-input" id="sortby1" name="sortby" value="number" checked><label
                class="form-check-label" for="sortby1">Number</label>
            </div>
            <div class="form-check">
              <input type="radio" class="form-check-input" id="sortby2" name="sortby" value="callerid" /><label
                class="form-check-label" for="sortby1">Caller ID</label>
            </div>
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button id="btnSubmitNumberAddExistingForm" class="btn btn-primary">Add</button>
        <button id="btnCancelNumberAddExistingForm" class="btn btn-dark" data-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
  </div>


</form>