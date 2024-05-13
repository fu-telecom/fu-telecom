<?php

class Result extends Controller
{
  private $name = "";
  private $errorOccured = false;
  private $log = "";

  public function __construct($name = null)
  {
    $this->name = $name;
  }

  public function GetName()
  {
    return $this->name;
  }

  public function GetResultData()
  {
    return $this->data;
  }

  public function AnErrorOccured()
  {
    $this->errorOccured = true;
  }

  public function AddErrorMessage($e)
  {
    $this->Log("<b>Error!!!</b><br><br>");
    $this->Log("<b>Error Message: </b>" . $e->getMessage() . "<br /><br />");
    $this->Log("<b>Trace: </b>" . $e->getTraceAsString() . "<br /><br />");
  }

  public function Log($text)
  {
    if (ProcessResult::$DUMP_LOG_TO_CONSOLE)
      echo $text;

    $this->log .= $text;
  }

  public function LogVariable($label, $variable)
  {
    $debugOutput = $label . ": <br>" .
      var_export($variable, true) . "<br><br>";

    $this->Log($debugOutput);
  }

  public function AddResultToXml(&$xml)
  {
    if ($this->name != null) {
      $child = $xml->addChild($this->name);
    } else {
      $child = $xml;
    }

    $child->addChild("error", (int) $this->errorOccured);
    $child->addChild("log", $this->log);
    $this->AsXML($child);

    return $xml;
  }

  public function AddArray($array)
  {
    foreach ($array as $name => $value) {
      $this->$name = $value;
    }
  }

  public function ToHtmlText()
  {
    $text = "<b>Result For " . $this->name . "</b><br>";
    $text .= "Error: " . (int) $this->errorOccured . "<br>";
    $text .= "Log: <p>" . $this->log . "</p><br><br>";

    foreach ($this->data as $name => $value)
      $text .= $name . ": " . $value . "<br>";

    $text .= "<br>";

    return $text;
  }
}


?>