<?php 

//include_once('includes/defaults.php');
//include_once('includes/db.php'); 

//include_once('functions/phone-functions.php');

include_once('FUConfig.php');

$currentRequest = new PageRequest($_REQUEST);

//$numberRequest = new NumberRequest($_REQUEST);
$numberController = new NumberController();
$numberController->SetupFromPageRequest($currentRequest);
var_dump($numberController);
echo "<br><br>";
$numberController->ProcessRequest();

var_dump($numberController);
echo "<br><br>";

$xml = $numberController->AsXML();

var_dump( $xml->asXML());
OutputXML($xml);


/*

if (isset($_GET['add'])) {
	$xml = new SimpleXMLElement('<xml/>');
				
	$phoneid = $_POST['phone_id'];
	$callerid = $_POST['callerid'];		
	$number = $_POST['number'];	
	$directory = $_POST['directory'];	
	$type = $_POST['type'];	
	$numberid = 0;
	
	$validate = 1;
	
	$added = 0;
	$exists = 0;
	$format = 1;
	
	$toomany = 0;
	$total = 0;
	$max = 0;
	
	if (isset($_GET['new'])) {
		$checkExisting = "SELECT * FROM numbers WHERE number LIKE :number";
		$checkStmt = $pdo->prepare($checkExisting);
		$checkStmt->execute(['number' => $number]);
		$rowCount = $checkStmt->fetchColumn();
		
		if ($rowCount > 0) {
			$exists = 1;
		}
	}
	
	if (strlen($number) == 0 Or strlen($callerid) == 0 Or !filter_var($number, FILTER_VALIDATE_INT) Or $exists == 1)
	{
		//Not valid!
		$validate = 0;
		$format = 0;
	} else {
		$maxLines = "SELECT phone_model_max_numbers FROM phone_models 
						INNER JOIN phones ON phones.phone_model_id = phone_models.phone_model_id
						WHERE phones.phone_id = ?;";
		$maxStmt = $pdo->prepare($maxLines);
		$maxStmt->execute([$phoneid]);
		$max = $maxStmt->fetch()['phone_model_max_numbers'];
		
		$checkTooMany = "SELECT count(*) as total FROM phone_number_assignment WHERE phone_id = ?;";
		$check = $pdo->prepare($checkTooMany);
		$check->execute([$phoneid]);
		$total = $check->fetch()['total'];
		
		if ($total >= $max) {
			$validate = 0;
			$toomany = 1;
		}
	}
	
	if ($validate == 1) {		
		//Validated, do add.
		$added = 1;
		
		AddNumber($callerid, $number, $directory, $type, $pdo);

		$numberid = $pdo->lastInsertId();
		
		AddNumberAssignment($phoneid, $numberid, $type, $pdo);
		
		SetPhoneStatusAltered($phoneid, 1, $pdo);
		
		$xml->addChild('updateList', $phoneid);
	}
	
	$xml->addChild('phone_id', $phoneid);
	$xml->addChild('number_id', $numberid);
	$xml->addChild('added', $added);
	$xml->addChild('validate', $validate);
	$xml->addChild('exists', $exists);
	$xml->addChild('toomany', $toomany);
	$xml->addChild('total', $total);
	$xml->addChild('max', $max);
	$xml->addChild('format', $format);
	
	OutputXML($xml);
	
} else if (isset($_GET['edit'])) {
	//Set all phones assigned to Altered
	$xml = new SimpleXMLElement('<xml/>');
	
	//Filling in unused elements that may be expected.
	$xml->addChild('exists', 0);
	$xml->addChild('added', 0);
	
	$phoneid = $_POST['phone_id'];
	$callerid = $_POST['callerid'];		
	$number = $_POST['number'];	
	$directory = $_POST['directory'];	
	$type = $_POST['type'];	
	$numberid = $_POST['number_id'];
	$validate = 1;
	$edited = 0;
	
	if (strlen($number) == 0 Or strlen($callerid) == 0 Or !filter_var($number, FILTER_VALIDATE_INT))
	{
		//Not valid!
		$validate = 0;		
	}
	
	if ($validate == 1) {		
		$updateQuery = "UPDATE numbers SET altered_number = 1, number = :number, callerid = :callerid, 
							number_type_id = :number_type_id, directory_id = :directory_id
						WHERE number_id = :number_id;";
		$update = $pdo->prepare($updateQuery);
		$update->execute(['number_id' => $numberid, 'number' => $number, 'callerid' => $callerid, 
			'number_type_id' => $type, 'directory_id' => $directory]);
			
		$edited = 1;
		
		SetPhoneStatusAltered($phoneid, 1, $pdo);
		
		$getPhoneListQry = "SELECT phone_id FROM phone_number_assignment WHERE number_id = ?";
		$getPhoneList = $pdo->prepare($getPhoneListQry);
		$getPhoneList->execute([$numberid]);
		
		while($phone = $getPhoneList->fetch()) {
			$xml->addChild("updateList", $phone['phone_id']);
		}
	}
	
	$xml->addChild('phone_id', $phoneid);
	$xml->addChild('number_id', $numberid);
	$xml->addChild('validate', $validate);
	$xml->addChild('edited', $edited);
	
	
	OutputXML($xml);
	
	
} else if (isset($_GET['delete'])) {
	//Delete Assignment
	$xml = new SimpleXMLElement('<xml/>');
	
	$deletedAll = 0;
	
	$phoneid = $_GET['phoneid'];
	$numberid = $_GET['numberid'];
	$assignmentid = $_GET['assignmentid'];
	
	//Check to see if it's marked for deletion or not. 
	$checkMarkQry = "SELECT todelete_assignment FROM phone_number_assignment WHERE phone_number_assignment_id = ?;";
	$checkMark = $pdo->prepare($checkMarkQry);
	$checkMark->execute([$assignmentid]);
	$todelete = $checkMark->fetch()['todelete_assignment'];
	
	//Invert deletion mark.
	$todelete = $todelete == 1 ? 0 : 1;
	
	$markDeletedQry = "UPDATE phone_number_assignment SET todelete_assignment = :todelete WHERE phone_number_assignment_id = :assignmentid;";
	$markDeleted = $pdo->prepare($markDeletedQry);
	$markDeleted->execute(['todelete' => $todelete, 'assignmentid' => $assignmentid]);	
	
	SetPhoneStatusAltered($phoneid, 1, $pdo);
	
	$xml->addChild('phone_id', $phoneid);
	$xml->addChild('number_id', $numberid);
	$xml->addChild('assignment_id', $assignmentid);
	$xml->addChild('todelete', $todelete);
	
	OutputXML($xml);
	
}

*/

?>