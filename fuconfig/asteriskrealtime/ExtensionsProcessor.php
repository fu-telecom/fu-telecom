<?php

//This is used to create the extensions file in /asterisk_scripts
//That file is used for the random call script
class ExtensionsProcessor {
  private $result;

  public function ProcessExtensions() {
    $this->result = new Result("ExtensionsProcessor");
    $this->result->Log("<br><h2>Extension Processing</h2>");

    $extensionList = array();
    $numberList = new NumberList();
    $numberList->LoadAll();

    foreach ($numberList->GetList() as $number) {
      if ($this->IsListedNumber($number)) {
        $extensionList[] = $number->GetAppNumber();
      }
    }

    $this->WriteExtensionsListFile($extensionList);
    $this->CombineWebAndCliFiles();

    return $this->result;
  }

  private function IsListedNumber($number):bool {

    if (!($number->number_type_id == NumberType::SIP or
          $number->number_type_id == NumberType::LINE))
      return false;

    $directory = $number->GetPhoneDirectory();

    if ($directory->directory_name == "Unlisted")
      return false;

    if ($number->number == 999 or $number->number == 911)
      return false;

    return true;
  }

  private function WriteExtensionsListFile($extensions) {
    $fileName = "/asterisk_scripts/extensions-list-web.txt";
    $file = fopen($fileName, "w+");
    $n = 0;

    $this->result->Log("<b>Writing Extensions File: </b><br/>");

    while ($n < count($extensions)) {
      $newLine = $extensions[$n] . "\n";
      //fprintf($file, '%s\r\n', $extensions[$n]);
      $this->result->Log("Line: " . $newLine . "<br />");
      fwrite($file, $newLine);
      $n = $n + 1;
    }

    $this->result->Log("<br />");

    fclose($file);
  }

  private function CombineWebAndCliFiles() {
    $this->result->Log("<br><b>Combining Extensions Files Into 1</b><br>");
    $this->result->Log(shell_exec('cat /asterisk_scripts/extensions-list-cli.txt > /asterisk_scripts/extensions-list.txt'));
    $this->result->Log(shell_exec('cat /asterisk_scripts/extensions-list-web.txt >> /asterisk_scripts/extensions-list.txt'));
    $this->result->Log(shell_exec('cat /asterisk_scripts/extensions-list-hardcoded.txt >> /asterisk_scripts/extensions-list.txt'));
  }


  //Below is the hard way.
  //Below is not debugged.
  /*
  private $extensionsFileData;


  public function ProcessExtensions($additionList, $deletionList) {
      $this->result = new Result();
      $this->result->Log("<br><h2>Extension Processing</h2>");

      $this->extensionsFileData = $this->GetExtensionsListFile();

      $this->ProcessDeletions($deletionList);
      $this->ProcessAdditions($additionList);

      $completedList = $this->MakeCompletedList();

      $this->WriteExtensionsListFile($completedList);
  }

  private function MakeCompletedList() {
    $completedList = array();

    foreach ($extensionsFileData as $extension) {
      if ($extension == null)
        continue;

      $completedList[] = $extension;
    }

    return $completedList;
  }

  private function ProcessDeletions($deletionList) {
    foreach ($deletionList as $number) {
      $this->result->Log("Removing Extension: " . $number->GetAppNumber() . "<br>");
      $this->RemoveExtension($number);
    }
  }

  private function ProcessAdditions($additionList) {
    foreach ($additionList$this->result->Log("<br><h2>Extension Processing</h2>"); as $number) {
      if (NumberExists($this->extensionsFileData, $number) == false) {
        $this->result->Log("Adding Extension: " . $number->GetAppNumber() . "<br>");
        $this->extensionsFileData[] = $number->GetAppNumber();
      } else {
        $this->result->Log("Extension already exists: " . $number->GetAppNumber() . "<br>");
      }
    }
  }

  private function RemoveExtension($number) {
    foreach ($this->extensionsFileData as &$extension) {
      //If this is true, it's already deleted.
      if ($extension == null)
        continue;

      //Cleanup any dead space from hand/script write-ins.
      if (strlen(trim($extension)) == 0) {
        $extension = null;
        continue;
      }

      //Cleanup badly written extension.
      if (strpos($extension, "/") == false) {
        $extension = null;
        continue;
      }

      //Take the extension apart to check sccpline_id
      $extensionParts = explode("/", $extension);

  		if ($extension == $number->GetAppNumber()
              Or $extensionParts[1] == $number->sccpline_id) {
  			//It exists. Wipe it.
        $extension = null;
  		}
  	}
  }

  private function NumberExists($extensions, $number) {
  	$exists = false;

  	foreach ($extensions as $extension) {
  		if ($extension == $number->GetAppNumber()) {
  			$exists = true;
  		}
  	}
  	//echo "Does Extension Exist?";
  	//var_dump($exists);
  	//echo "<br />";

  	return $exists;
  }

  private function GetExtensionsListFile() {
  	$fileName = "/asterisk_scripts/extensions-list.txt";
  	$file = fopen($fileName, "r");
  	$extenions = array();

  	$this->result->Log("<b>Reading Extensions File: </b><br/>");

  	while (! feof($file))
  	{
  		$line = fgets($file);
  		$this->result->Log("Line: " . $line . "<br />");
  		if (strlen($line) > 0) $extensions[] = trim($line);
  	}

  	$this->result->Log("<br />");

  	fclose($file);

  	return $extensions;
  }

  private function WriteExtensionsListFile($extensions) {
  	$fileName = "/asterisk_scripts/extensions-list.txt";
  	$file = fopen($fileName, "w+");
  	$n = 0;

    $this->result->Log("<b>Writing Extensions File: </b><br/>");

  	while ($n < count($extensions)) {
  		$newLine = $extensions[$n] . "\n";
  		//fprintf($file, '%s\r\n', $extensions[$n]);
  		$this->result->Log("Line: " . $newLine . "<br />");
  		fwrite($file, $newLine);
  		$n = $n + 1;
  	}

  	$this->result->Log("<br />");

  	fclose($file);
  }*/
}


 ?>
