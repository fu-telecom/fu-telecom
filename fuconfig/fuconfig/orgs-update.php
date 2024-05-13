<?php

include_once ('FUConfig.php');

$currentRequest = new PageRequest($_REQUEST);


$org = new Org();
$org->LoadFromPageRequest($currentRequest);
var_dump($org);

if ($currentRequest->IsUpdateRequest() or $currentRequest->IsCreateRequest()) {
  $org->SaveToDB();
} else if ($currentRequest->IsDeleteRequest()) {
  //First check to make sure there are no phones assigned.
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

/*
include_once('includes/defaults.php');
include_once('includes/db.php');

if (isset($_GET['edit'])) {

  $editQuery = "UPDATE orgs SET org_name = :name, org_contactname = :contactname,
            org_contactphone = :contactphone, org_contactemail = :contactemail
          WHERE org_id = :id;";

  $editStmt = $pdo->prepare($editQuery);
  $editStmt->execute(['id' => $_POST['id'], 'name' => $_POST['name'], 'contactname' => $_POST['contactname'],
    'contactemail' => $_POST['contactemail'], 'contactphone' => $_POST['contactphone']]);

  Redirect('/index.php');


} else if (isset($_GET['add'])) {

  $addQuery = "INSERT INTO orgs (org_name, org_contactname, org_contactemail, org_contactphone)
          VALUES (:name, :contactname, :contactemail, :contactphone);";

  $addStmt = $pdo->prepare($addQuery);
  $addStmt->execute(['name' => $_POST['name'], 'contactname' => $_POST['contactname'],
    'contactemail' => $_POST['contactemail'], 'contactphone' => $_POST['contactphone']]);

  Redirect('/index.php');

} else if (isset($_GET['delete'])) {

  $delQuery = "DELETE FROM orgs WHERE org_id = ?;";
  $delStmt = $pdo->prepare($delQuery);
  $delStmt->execute([$_GET['id']]);

}*/




?>