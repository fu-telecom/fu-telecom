<?php

//TODO: Remove this, if the switch to web config worked.
//      It will be obsolete since we've moved to the
//      web config only. This was solely for use with hardcoded/scripted phones.
class ButtonsProcessor
{
  private $result;

  public function ProcessButtons()
  {
    $this->result = new Result("ButtonsProcessor");
    $this->result->Log("<br><h2>Buttons Processing</h2>");

    $numberList = new NumberList();
    $numberList->LoadAll();
    $buttons = array();

    foreach ($numberList->GetList() as $number) {
      $buttonText = "button - speeddial, " . $number->callerid . ", " .
        $number->number . ", " . $number->number . "@hints";

      $buttons[] = $buttonText;
    }

    $this->WriteButtonsFile($buttons);

    $this->CombineWebAndCliFiles();
    $this->GeneratePhoneButtonLists();

    return $this->result;
  }


  private function WriteButtonsFile($buttons)
  {
    $fileName = "/asterisk_scripts/buttons_list/buttons-list-web.txt";
    $file = fopen($fileName, "w+");
    $n = 0;

    $this->result->Log("<b>Writing Buttons File: </b><br/>");

    while ($n < count($buttons)) {
      $newLine = $buttons[$n] . "\n";
      $this->result->Log("Line: " . $newLine . "<br />");
      fwrite($file, $newLine);
      $n = $n + 1;
    }

    $this->result->Log("<br />");

    fclose($file);
  }

  private function CombineWebAndCliFiles()
  {
    $this->result->Log("<br><b>Combining Buttons Files Into 1</b><br>");
    $this->result->Log(shell_exec('cat /asterisk_scripts/buttons_list/buttons-list-hardcoded.txt > /asterisk_scripts/buttons_list/buttons-list.txt'));
    $this->result->Log(shell_exec('cat /asterisk_scripts/buttons_list/buttons-list-cli.txt >> /asterisk_scripts/buttons_list/buttons-list.txt'));
    $this->result->Log(shell_exec('cat /asterisk_scripts/buttons_list/buttons-list-web.txt >> /asterisk_scripts/buttons_list/buttons-list.txt'));
  }

  private function GeneratePhoneButtonLists()
  {
    $this->result->Log("<br><b>Generating 7960/7965 lists</b><br>");
    $this->result->Log(shell_exec('/asterisk_scripts/buttons_list/buttons-list-7960.sh'));
    $this->result->Log(shell_exec('/asterisk_scripts/buttons_list/buttons-list-7965.sh'));
  }

}

?>