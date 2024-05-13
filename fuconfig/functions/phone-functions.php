<?php



function SetPhoneStatusAltered($phoneId, $altered, $pdo)
{
  $updateQry = "UPDATE phones SET altered = :altered WHERE phone_id = :phoneid";
  $stmt = $pdo->prepare($updateQry);
  $stmt->execute(['altered' => $altered, 'phoneid' => $phoneId]);
}

function SetPhoneStatusAdded($phoneId, $added, $pdo)
{
  $updateQry = "UPDATE phones SET added = :added WHERE phone_id = :phoneid";
  $stmt = $pdo->prepare($updateQry);
  $stmt->execute(['added' => $added, 'phoneid' => $phoneId]);
}

function AddNumberAssignment($phoneid, $numberid, $numbertypeid, $pdo)
{
  $updateQry = "INSERT INTO phone_number_assignment (number_id, phone_id, number_type_id, added_assignment) VALUES (?,?,?,1);";
  $update = $pdo->prepare($updateQry);
  $update->execute([$numberid, $phoneid, $numbertypeid]);
}

function AddNumber($callerid, $number, $directory, $type, $pdo)
{
  $addNumberQry = "INSERT INTO numbers (callerid, number, directory_id, number_type_id, added_number)
				VALUES (:callerid, :number, :directory, :type, 1);";
  $addNumberStmt = $pdo->prepare($addNumberQry);

  $addNumberStmt->execute(['callerid' => $callerid, 'number' => $number, 'directory' => $directory, 'type' => $type]);
}

function AddLineIdToNumber($numberid, $sccplineid, $pdo)
{
  echo " --- AddLineIdToNumber Called: " . $numberid . " - " . $sccplineid;
  echo "<br /><br />";
  $updateQry = "UPDATE fuconfig.numbers SET sccpline_id = ? WHERE number_id = ?;";
  $update = $pdo->prepare($updateQry);
  $update->execute([$sccplineid, $numberid]);
}


?>