<?php

include_once ('FUConfig.php');

$currentRequest = new PageRequest($_REQUEST);

$org = new Org();
$org->LoadFromPageRequest($currentRequest);
var_dump($org);

if ($currentRequest->IsUpdateRequest() or $currentRequest->IsCreateRequest()) {
  $org->SaveToDB();
} else if ($currentRequest->IsDeleteRequest()) {
  // First check to make sure there are no phones assigned.
  $org_id = $currentRequest->GetID();

  $phoneList = new PhoneList();
  $phoneList->LoadOrgPhones($org_id);

  echo $phoneList->GetCount();

  if ($phoneList->GetCount() == 0) {
    $org->LoadFromDB($org_id);
    $org->DeleteFromDB();
  } else {
    $trace = debug_backtrace();
    trigger_error(
      'Cannot delete org with assigned phones: ' .
      ' in ' . $trace[0]['file'] .
      ' on line ' . $trace[0]['line'],
      E_USER_ERROR
    );
  }
} else {
  $trace = debug_backtrace();
  trigger_error(
    'Invalid request type: ' .
    ' in ' . $trace[0]['file'] .
    ' on line ' . $trace[0]['line'],
    E_USER_ERROR
  );
}

Redirect('/index.php');
