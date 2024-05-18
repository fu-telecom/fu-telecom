<?php

//This is used to create the extensions file in /asterisk_scripts
//That file is used for the random call script
class ExtensionsProcessor
{
  private $result;

  public function ProcessExtensions()
  {
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

  private function IsListedNumber($number): bool
  {

    if (
      !($number->number_type_id == NumberType::SIP or
        $number->number_type_id == NumberType::LINE)
    )
      return false;

    $directory = $number->GetPhoneDirectory();

    if ($directory->directory_name == "Unlisted")
      return false;

    if ($number->number == 999 or $number->number == 911)
      return false;

    return true;
  }

  private function WriteExtensionsListFile($extensions)
  {
    $fileName = "/asterisk_scripts/extensions-list-web.txt";
    $file = fopen($fileName, "w+");
    $n = 0;

    $this->result->Log("<b>Writing Extensions File: </b><br/>");

    while ($n < count($extensions)) {
      $newLine = $extensions[$n] . "\n";
      $this->result->Log("Line: " . $newLine . "<br />");
      fwrite($file, $newLine);
      $n = $n + 1;
    }

    $this->result->Log("<br />");

    fclose($file);
  }

  private function CombineWebAndCliFiles()
  {
    $this->result->Log("<br><b>Combining Extensions Files Into 1</b><br>");
    $this->result->Log(shell_exec('cat /asterisk_scripts/extensions-list-cli.txt > /asterisk_scripts/extensions-list.txt'));
    $this->result->Log(shell_exec('cat /asterisk_scripts/extensions-list-web.txt >> /asterisk_scripts/extensions-list.txt'));
    $this->result->Log(shell_exec('cat /asterisk_scripts/extensions-list-hardcoded.txt >> /asterisk_scripts/extensions-list.txt'));
  }
}


?>