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

?>