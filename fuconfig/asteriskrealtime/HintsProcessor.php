<?php

class HintsProcessor
{
  private $result;

  public function ProcessHints()
  {
    $this->result = new Result("HintsProcessor");
    $this->result->Log("<br><h2>Hints Processing</h2>");

    $this->ProcessHintsFile();
    $this->ReloadDialplan();

    return $this->result;
  }

  private function ReloadDialplan()
  {
    $reloadCmd = 'sudo asterisk -x "dialplan reload"';
    $this->result->Log("<h3>Reloading Dialplan<h3><br>");
    $this->result->Log(shell_exec($reloadCmd));
  }

  private function ProcessHintsFile()
  {
    $numberList = new NumberList();
    $numberList->LoadAll();
    $hints = array();

    foreach ($numberList->GetList() as $number) {
      if (
        !($number->number_type_id == NumberType::SIP or
          $number->number_type_id == NumberType::LINE)
      )
        continue;


      if ($number->number_type_id == NumberType::SIP) {
        $hintText = "exten => " . $number->number . ",hint,SIP/" . $number->sip_user;
      } else {
        $hintText = "exten => " . $number->number . ",hint," . $number->GetAppNumber();
      }

      $hints[] = $hintText;
    }

    $this->WriteHintsFile($hints);
  }


  private function WriteHintsFile($hints)
  {
    $fileName = "/asterisk_scripts/gui-hints-list.txt";
    $file = fopen($fileName, "w+");
    $n = 0;

    $this->result->Log("<b>Writing Hints File: </b><br/>");

    while ($n < count($hints)) {
      $newLine = $hints[$n] . "\n";
      $this->result->Log("Line: " . $newLine . "<br />");
      fwrite($file, $newLine);
      $n = $n + 1;
    }

    $this->result->Log("<br />");

    fclose($file);
  }

}

?>