<?php


include_once ('includes/defaults.php');
include_once ('includes/db.php');

function CreateHint($number, $type)
{
  return "exten => " . $number . ",hint," . $type . "/" . $number;
}

function WriteHintsFile()
{
  global $pdo;

  $fileName = "/asterisk_scripts/gui-hints-list.txt";
  $file = fopen($fileName, "w+");

  $numberQry = "SELECT numbers.number, phone_type_name, numbers.callerid, number_types.number_type_system_name
					FROM phones
					INNER JOIN phone_number_assignment ON phones.phone_id = phone_number_assignment.phone_id
					INNER JOIN numbers ON phone_number_assignment.number_id = numbers.number_id
					INNER JOIN number_types ON numbers.number_type_id = number_types.number_type_id
					INNER JOIN phone_types ON phones.phone_type_id = phone_types.phone_type_id
					INNER JOIN phone_models ON phones.phone_model_id = phone_models.phone_model_id
					WHERE number_type_system_name LIKE 'line';";
  $numbers = $pdo->prepare($numberQry);
  $numbers->execute();

  while ($number = $numbers->fetch()) {
    $line = CreateHint($number['number'], $number['phone_type_name']) . "\n";
    fwrite($file, $line);
  }

  fclose($file);
}

?>