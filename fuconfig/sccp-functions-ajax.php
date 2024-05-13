<?php

//TODO: Update this file to use the new dataclasses completely.
include_once ('FUConfig.php');

$pageRequest = new PageRequest($_REQUEST);

include_once ('includes/defaults.php');
include_once ('includes/db.php');

$request = $_GET['request'];

if ($request == "reload") {
  $xml = new SimpleXMLElement('<xml/>');
  $phoneid = $_GET['phone_id'];

  $getPhoneSerialQry = "SELECT * FROM phones WHERE phone_id = ?;";
  $getPhoneSerial = $pdo->prepare($getPhoneSerialQry);
  $getPhoneSerial->execute([$phoneid]);

  $serial = $getPhoneSerial->fetch()['phone_serial'];

  $reloadcmd = 'sudo asterisk -x "sccp reload device ' . $serial . '"';
  $result = shell_exec($reloadcmd);

  $xml->addChild("result", $result);
  $xml->addChild("phoneid", $phoneid);

  OutputXML($xml);
} else if ($request == "restart") {
  $xml = new SimpleXMLElement('<xml/>');
  $phoneid = $_GET['phone_id'];

  $getPhoneSerialQry = "SELECT * FROM phones WHERE phone_id = ?;";
  $getPhoneSerial = $pdo->prepare($getPhoneSerialQry);
  $getPhoneSerial->execute([$phoneid]);

  $serial = $getPhoneSerial->fetch()['phone_serial'];

  $reloadcmd = 'sudo asterisk -x "sccp restart ' . $serial . '"';
  $result = shell_exec($reloadcmd);

  $xml->addChild("result", $result);
  $xml->addChild("phoneid", $phoneid);

  OutputXML($xml);
} else if ($request == "redo") {
  $phone = Phone::LoadPhoneByID($pageRequest->phone_id);

  //Remove the phone and reload it.
  $sccpProcessor = new SccpProcessor();
  $sccpProcessor->DeletePhoneAsterisk($phone);
  $sccpProcessor->AddPhoneAsterisk($phone);
  $sccpProcessor->ReloadPhone($phone);

  $xml = new SimpleXMLElement('<xml/>');
  $result = new Result();
  $result->phone_id = $phone->phone_id;
  $result->result = "Phone is redone, except for any lines.";
  $result->AddResultToXml($xml);
  OutputXML($xml);
}


?>