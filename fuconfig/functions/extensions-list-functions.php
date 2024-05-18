<?php


include_once ('includes/defaults.php');
include_once ('includes/db.php');


function GetExtensionsListFile()
{
  $fileName = "/asterisk_scripts/extensions-list.txt";
  $file = fopen($fileName, "r");
  $extenions = array();

  echo "<b>Reading Extensions File: </b><br/>";

  while (!feof($file)) {
    $line = fgets($file);
    echo "Line: " . $line . "<br />";
    if (strlen($line) > 0)
      $extensions[] = trim($line);
  }

  echo "<br /><br />";

  fclose($file);

  return $extensions;
}

function WriteExtensionsListFile($extensions)
{
  $fileName = "/asterisk_scripts/extensions-list.txt";
  $file = fopen($fileName, "w+");
  $n = 0;

  echo "<b>Writing Extensions File: </b><br/>";

  while ($n < count($extensions)) {
    $newLine = $extensions[$n] . "\n";
    echo "Line: " . $newLine . "<br />";
    fwrite($file, $newLine);
    $n = $n + 1;
  }

  echo "<br /><br />";

  fclose($file);
}

function ExtensionExists($extensions, $search)
{
  $exists = false;

  foreach ($extensions as $extension) {
    if ($extension == $search) {
      $exists = true;
    }
  }

  return $exists;
}

function RemoveExisting($extensionsList, $existingList)
{
  $completedList = array();
  echo "<b>Removing Old Extensions: </b><br />";
  echo "Existing List: ";
  var_dump($existingList);
  echo "<br />";

  foreach ($extensionsList as $extension) {
    if (!ExtensionExists($existingList, $extension)) {
      $completedList[] = $extension;
      echo "Adding Extension: " . $extension . "<br />";
    } else {
      echo "Removing Extension: " . $extension . "<br />";
    }
  }

  echo "<br /><br />";

  return $completedList;
}

function ProcessExtensionsList($extensionsToAdd, $extensionsToRemove)
{
  $currentExtensions = GetExtensionsListFile();
  $currentExtensions = RemoveExisting($currentExtensions, $extensionsToRemove);

  //Add new extensions to list.
  foreach ($extensionsToAdd as $extension) {
    $currentExtensions[] = $extension;
  }

  WriteExtensionsListFile($currentExtensions);
}



?>