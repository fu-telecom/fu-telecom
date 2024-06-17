<?php

include_once ('FUConfig.php');

$resultProcessor = new ProcessResult(false);

// Update Asterisk Realtime database
$processor = new PhoneProcessor($resultProcessor);
$processor->ProcessAllPhones();

// Write files
$directoryProcessor = new DirectoryProcessor();
$resultProcessor->AddResult($directoryProcessor->ProcessDirectories());
$extensionsProcessor = new ExtensionsProcessor();
$resultProcessor->AddResult($extensionsProcessor->ProcessExtensions());
$hintsProcessor = new HintsProcessor();
$resultProcessor->AddResult($hintsProcessor->ProcessHints());
$buttonsProcessor = new ButtonsProcessor();
$resultProcessor->AddResult($buttonsProcessor->ProcessButtons());

$resultXml = $resultProcessor->GetXmlResult();

$xpathToError = "/xml/PhoneProcessorResult/error";
$error = $resultXml->xpath($xpathToError)[0];

if ($error != null) {
  if ($error == 1) {
    // An error occurred. Write the file somewhere.
    $xpathToTime = "/xml/TimeStamp";
    $timestamp = $resultXml->xpath($xpathToTime)[0];
    $fileDestination = "/asterisk_scripts/error_log/" . $timestamp . ".log";
    $resultProcessor->LogText("Writing Error Log:" . $fileDestination . "<br>");

    // Prepare it as a usable document.
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
