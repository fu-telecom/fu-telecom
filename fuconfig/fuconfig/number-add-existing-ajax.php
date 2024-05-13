<?php

include_once ('FUConfig.php');

$currentRequest = new PageRequest($_REQUEST);

if ($currentRequest->IsReviewRequest()) {
  $numberList = new NumberList();
  $numberList->LoadAllWithSort($currentRequest->sortby);

  //To output.
  $xml = new SimpleXMLElement('<xml/>');

  foreach ($numberList->GetList() as $number) {
    $currentChild = $xml->addChild('numberItem');
    $number->AsXML($currentChild);
    $text = $number->number . " - " . $number->callerid . " - " . $number->GetDefaultNumberType()->number_type_name;
    $currentChild->addChild('text', $text);
  }

  OutputXML($xml);

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

/*


include_once('includes/defaults.php');
include_once('includes/db.php'); 

include_once('functions/phone-functions.php');

$request = $_GET['request'];

if ($request == "gettype") {
  $xml = new SimpleXMLElement('<xml/>');
  //$type_id = $_GET['type_id'];
  $sortby = $_GET['sortby'];
  
  if ($sortby == "number_type_name") {
    $sortby .= ", callerid";
  }
  
  $numberListQry = "SELECT * FROM numbers 
            INNER JOIN number_types ON numbers.number_type_id = number_types.number_type_id
            ORDER BY ?;";
  $numberList = $pdo->prepare($numberListQry);
  $numberList->execute([$sortby]);
  
  while ($number = $numberList->fetch()) {
    $current = $xml->addChild('numberItem');
    $current->addChild('number_id', $number['number_id']);
    $current->addChild('number', $number['number']);
    $current->addChild('callerid', $number['callerid']);
    $current->addChild('text', $number['number'] . " - " . $number['callerid'] . " - " . $number['number_type_name']);
    
  }
  
  OutputXML($xml);
  
} else if ($request == "add") {
  //Adding existing so we just process the assignment.
  $phoneid = $_POST['phone_id'];
  $numberid = $_POST['number'];
  $numbertypeid = $_POST['type'];
  
  AddNumberAssignment($phoneid, $numberid, $numbertypeid, $pdo);
  
  SetPhoneStatusAltered($phoneid, 1, $pdo);
  
  $xml = new SimpleXMLElement('<xml/>');
  $xml->addChild("phone_id", $phoneid);
  $xml->addChild("number_id", $numberid);
  $xml->addChild("number_type_id", $numbertypeid);
  $xml->addChild("added", "1");
  OutputXML($xml);
}*/


?>