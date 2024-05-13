<?php


include_once ('includes/defaults.php');
include_once ('includes/db.php');

function PrintPlaintextDirectory()
{
  global $pdo;

  $fileName = "/tftproot/directory/gui-plaintext-directory.txt";
  $file = fopen($fileName, "w+");

  $numberQry = "SELECT numbers.number, phone_type_name, numbers.callerid, directories.directory_filename, org_name
					FROM phones
					INNER JOIN phone_number_assignment ON phones.phone_id = phone_number_assignment.phone_id
					INNER JOIN numbers ON phone_number_assignment.number_id = numbers.number_id
					INNER JOIN phone_types ON phones.phone_type_id = phone_types.phone_type_id
					INNER JOIN phone_models ON phones.phone_model_id = phone_models.phone_model_id
					INNER JOIN directories ON numbers.directory_id = directories.directory_id
					INNER JOIN orgs ON phones.phone_org_id = orgs.org_id;";
  $numbers = $pdo->prepare($numberQry);
  $numbers->execute();

  while ($number = $numbers->fetch()) {
    $line = $number['org_name'] . " " . $number['callerid'] . " " . $number['number'] . " " . $number['directory_filename'] . "\n";
    fwrite($file, $line);
  }

  fclose($file);
}

?>