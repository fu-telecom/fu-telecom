<?php


include_once ('includes/defaults.php');
include_once ('includes/db.php');


function GetButtonsListFile()
{
  $fileName = "/asterisk_scripts/buttons_list/buttons-list.txt";
  $file = fopen($fileName, "r");
  $buttons = array();

  echo "<b>Reading Buttons File: </b><br/>";

  while (!feof($file)) {
    $line = fgets($file);
    echo "Line: " . $line . "<br />";
    if (strlen($line) > 0)
      $buttons[] = trim($line);
  }

  echo "<br /><br />";

  fclose($file);

  return $buttons;
}

function WriteButtonsListFile($buttons)
{
  $fileName = "/asterisk_scripts/buttons_list/buttons-list.txt";
  $file = fopen($fileName, "w+");
  $n = 0;

  echo "<b>Writing Buttons File: </b><br/>";

  while ($n < count($buttons)) {
    $newLine = $buttons[$n] . "\n";
    echo "Line: " . $newLine . "<br />";
    fwrite($file, $newLine);
    $n = $n + 1;
  }

  echo "<br /><br />";

  fclose($file);
}

function ButtonExists($numberList, $button)
{
  $exists = false;

  foreach ($numberList as $number) {
    if (substr_count($button, CreateButtonSearch($number)) > 0) {
      $exists = true;
    }
  }

  return $exists;
}

function RemoveExistingButtons($buttonsList, $existingNumbersList)
{
  $completedList = array();
  echo "<b>Removing Old Buttons: </b><br />";

  foreach ($buttonsList as $button) {
    if (!ButtonExists($existingNumbersList, $button)) {
      $completedList[] = $button;

      echo "Adding Button: " . $button . "<br />";
    } else {
      echo "Removing Button: " . $button . "<br />";
    }
  }

  echo "<br /><br />";

  return $completedList;
}

function CreateButton($number, $callerid)
{
  $text = "button = speeddial, " . $callerid . ", " . $number . ", " . $number . "@hints";

  return $text;
}

function CreateButtonSearch($number)
{
  return $number . ", " . $number . "@hints";
}

function ProcessButtonsList($linesToAdd, $numbersToRemove)
{
  $currentButtons = GetButtonsListFile();
  $currentButtons = RemoveExistingButtons($currentButtons, $numbersToRemove);

  //Add new extensions to list.
  foreach ($linesToAdd as $line) {
    $currentButtons[] = CreateButton($line['number'], $line['callerid']);
  }

  WriteButtonsListFile($currentButtons);

  $create7965 = "/asterisk_scripts/buttons_list/buttons-list-7965.sh";
  $create7960 = "/asterisk_scripts/buttons_list/buttons-list-7960.sh";




}



?>