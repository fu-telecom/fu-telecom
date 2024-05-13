<?php

class DirectoryProcessor
{

  private $result = null;

  public function ProcessDirectories()
  {
    //Rewrite to not cooperate with the cli install scripts.
    $this->result = new Result("Directories");

    $this->result->Log("<br><h2>Directory Processing</h2>");
    $directoryList = PhoneDirectoryList::LoadPhoneDirectoryList();

    //Foreach directory
    foreach ($directoryList->GetList() as $directory) {
      //Skip adding unlisted.
      if ($directory->directory_name == "Unlisted")
        continue;

      $this->result->Log("Processing Directory " . $directory->directory_name .
        " : " . $directory->directory_filename . "<br>");

      $this->WriteDirectory($directory);
    }

    return $this->result;
  }

  private function MakeEmptyDirectory($directory)
  {
    $this->result->Log("Replacing file with empty directory.<br>");

    $folder = "/var/www/html/directory/";
    $filename = $directory->directory_filename;
    $cmd = "cp {$folder}{$filename}.empty {$folder}{$filename}";

    shell_exec($cmd);
  }

  private function WriteDirectory($directory)
  {
    $numberList = new NumberList();
    $numberList->LoadByDirectory($directory);

    //Replace with empty directory file.
    $this->MakeEmptyDirectory($directory);

    //Read in now emptied file.
    $xml = $this->GetDirectoryFileXml($directory);

    foreach ($numberList->GetList() as $number) {
      $this->AddNumber($number, $xml);
    }

    //Save completed directory to file.
    $this->SaveDirectoryFileXml($directory, $xml);
  }

  private function AddNumber($number, &$xml)
  {
    $this->result->Log("Adding Number: " . $number->callerid . " - " . $number->number . "<br>");

    $entry = $xml->addChild("DirectoryEntry");
    $entry->addChild("Name", $number->callerid);
    $entry->addChild("Telephone", $number->number);
  }

  private function GetFileName($directory)
  {
    $fileName = "/var/www/html/directory/" . $directory->directory_filename;

    return $fileName;
  }

  private function GetDirectoryFileXml($directory)
  {
    $fileName = $this->GetFileName($directory);
    $fileContents = file_get_contents($fileName);
    return simplexml_load_string($fileContents);
  }

  private function SaveDirectoryFileXml($directory, &$xml)
  {
    //Output with formatting.
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    $dom->save($this->GetFileName($directory));
  }
}



?>