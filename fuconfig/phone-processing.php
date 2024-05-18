<?php

include_once ('FUConfig.php');

$resultProcessor = new ProcessResult(false);

$directoryProcessor = new DirectoryProcessor();
$extensionsProcessor = new ExtensionsProcessor();
$hintsProcessor = new HintsProcessor();
$buttonsProcessor = new ButtonsProcessor();

$processor = new PhoneProcessor($resultProcessor);
$processor->ProcessAllPhones();

$resultProcessor->AddResult($directoryProcessor->ProcessDirectories());
$resultProcessor->AddResult($extensionsProcessor->ProcessExtensions());
$resultProcessor->AddResult($hintsProcessor->ProcessHints());
$resultProcessor->AddResult($buttonsProcessor->ProcessButtons());

$resultXml = $resultProcessor->GetXmlResult();

$xpathToError = "/xml/PhoneProcessorResult/error";
$xpathToTime = "/xml/TimeStamp";

$error = $resultXml->xpath($xpathToError)[0];
$timestamp = $resultXml->xpath($xpathToTime)[0];

if ($error != null) {
  if ($error == 1) {

    //An error occurred. Write the file somewhere.
    $fileDestination = "/asterisk_scripts/error_log/" . $timestamp . ".log";
    $resultProcessor->LogText("Writing Error Log:" . $fileDestination . "<br>");

    //Prepare it as a usable document.
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXml($resultXml->asXML());
    $dom->save($fileDestination);
  }

} else {
  throw new Exception("Can't find error xml in result.");
}

OutputXML($resultXml);


?>