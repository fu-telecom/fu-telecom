<?php

class ProcessResult
{
  public static $DUMP_LOG_TO_CONSOLE = false;

  private $resultList = array();

  private $xmlResult = null;
  private $textResult = null;

  private $logText = "";
  private $time = null;

  public function __construct($dumpToLog = false)
  {
    self::$DUMP_LOG_TO_CONSOLE = $dumpToLog;

    $this->time = date('Y-m-d_hia');
  }

  public function GetXmlResult()
  {
    if ($this->xmlResult == null)
      $this->ProcessAsXml();

    return $this->xmlResult;
  }

  public function GetTextResult()
  {
    if ($this->textResult == null)
      $this->ProcessAsText();

    return $this->textResult;
  }

  public function GetLog()
  {
    return $this->logText;
  }

  public function LogText($text)
  {
    if (ProcessResult::$DUMP_LOG_TO_CONSOLE)
      echo $text;

    $this->logText .= $text;
  }

  public function AddResult($result)
  {
    $this->resultList[] = $result;

    $this->logText .= "<br>" . $result->ToHtmlText();
  }

  public function ProcessAsXML()
  {
    $xml = new SimpleXMLElement('<xml/>');
    $fullLog = $xml->addChild("FullLog", $this->logText);
    $timeStamp = $xml->addChild("TimeStamp", $this->time);

    foreach ($this->resultList as $result) {
      $result->AddResultToXml($xml);
    }

    $this->xmlResult = $xml;

    return $xml;
  }

  public function ProcessAsText()
  {
    $resultText = "<h2>Process Result</h2><br><br>";
    $resultText .= "<h3>Full Log</h3><p>" . $this->logText . "</p><br><br>";

    foreach ($resultList as $result) {
      $resultText .= $result->ToHtmlText();
    }

    $this->textResult = $resultText;

    return $resultText;
  }



  public function AsXML($includeLog)
  {

  }
}



?>