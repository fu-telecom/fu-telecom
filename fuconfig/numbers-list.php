<?php

include_once ('FUConfig.php');

if (isset($phone) == false) {
  $currentRequest = new PageRequest($_REQUEST);
  $phone = Phone::LoadPhoneByID($currentRequest->GetID());
}

$numberList = new NumberList();
$numberList->LoadByPhoneAssignment($phone->phone_id);

?>


<div class="col ml-4">

  <?php if ($numberList->GetCount() > 0) { ?>
    <table class="table">
      <thead>
        <tr>
          <th scope="col">&nbsp;</th>
          <th scope="col">Number</th>
          <th scope="col">Caller ID</th>
          <th scope="col">Assigned/Line Type</th>
          <th scope="col">Directory</th>
          <?php if ($phone->phone_type_id == PhoneType::SIP) { ?>
            <th scope="col">User/Pass</th>
          <?php } ?>
          <th scope="col">Options</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($numberList->GetList() as $number) {

          $assignment = $number->GetAssignmentByPhone($phone->phone_id);
          $assignmentType = $assignment->GetNumberType();
          $numberType = $assignment->GetNumber()->GetDefaultNumberType();

          //CSS determination
          $cellClass = $assignment->todelete_assignment == 1 ? 'strikeout' : '';
          $deleteBtnClass = $assignment->todelete_assignment == 1 ? 'btn-danger' : 'btn-warning';

          ?>
          <tr>
            <th scope="row" class="<?= $cellClass ?>">
              <?= $assignment->display_order ?>
            </th>
            <th scope="row" class="<?= $cellClass ?>">
              <?= $number->number ?>
            </th>
            <td class="<?= $cellClass ?>">
              <?= $number->callerid ?>
            </td>
            <td class="<?= $cellClass ?>">
              <?= $assignmentType->number_type_name ?> / <?= $numberType->number_type_name ?>
            </td>
            <td class="<?= $cellClass ?>">
              <?= $number->GetPhoneDirectory()->directory_name ?>
            </td>
            <?php if ($phone->phone_type_id == PhoneType::SIP) { ?>
              <td>
                <?= $number->sip_user ?> / <?= $number->sip_pass ?>
              </td>
            <?php } ?>
            <td>
              <button class="btn btn-primary" id="btnEditNumber<?= $number->number_id ?>"
                onClick="editNumber(<?= $phone->phone_id ?>, <?= $number->number_id ?>)">
                Edit
              </button>

              <button class="btn <?= $deleteBtnClass ?>" id="btnDeleteNumber<?= $number->number_id ?>"
                onClick="deleteNumber(<?= $phone->phone_id ?>, <?= $number->number_id ?>, <?= $assignment->phone_number_assignment_id ?>)">
                <?= $assignment->todelete_assignment == 1 ? 'Un-remove' : 'Remove' ?>
              </button>
            </td>
          </tr>
        <?php } ?>
      </tbody>

    </table>

  <?php } else { ?>
    <div class="row alert-warning p-1 rounded">
      <strong>No Numbers Added</strong>
    </div>
  <?php } ?>

</div>