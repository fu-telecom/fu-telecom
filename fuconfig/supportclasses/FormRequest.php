<?php

class FormRequest extends PageRequest
{

  const GET = 1;
  const POST = 2;
  const NONE = 0;

  private $methodConst;
  private $formId;

  public function __construct($formId = "", $requestTypeConstant, $requestPage, $requestMethodConst = 0)
  {
    $this->formId = $formId;
    $this->methodConst = $requestMethodConst;
    $this->Init($requestTypeConstant, $requestPage);
  }

  public function GetFormID(): string
  {
    return $this->formId;
  }

  //Creates a hidden field form from a data class's table fields. 
  //Useful for AJAX methods. 
  public function OutputDataClassAsHiddenForm($dataClass, $additionalText = "")
  {
    $form = $this->CreateFormHeader() . "\n";
    $data = $dataClass->GetTableData();

    foreach ($data as $field => $value) {
      $form .= "\t" . $this->CreateHiddenInput($field, $value) . "\n";
    }

    if (strlen($additionalText) > 0) {
      $form .= "\t" . $additionalText . "\n";
    }

    $form .= "</form> \n";

    return $form;
  }

  public function CreateFormHeader()
  {
    $formHeader = "<form id=\"" . $this->formId . "\" name=\"" . $this->formId . "\" ";

    //Set method tag. 
    if ($this->methodConst == FormRequest::GET)
      $formHeader .= "method=\"GET\" ";
    else if ($this->methodConst == FormRequest::POST)
      $formHeader .= "method=\"POST\" ";
    else if ($this->methodConst == FormRequest::NONE)
      $formHeader .= "";
    else {
      $trace = debug_backtrace();
      trigger_error(
        'Invalid Form Method: ' . $methodConst .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_ERROR
      );
      return null;
    }

    $formHeader .= "action=\"" . $this->GetRequestAction() . "\">";

    return $formHeader;
  }

  public function CreateHiddenInput($name, $value)
  {
    $input = "<input type=\"hidden\" id=\"" . $name . "\" name=\"" . $name . "\" value=\"" . $value . "\"/>";

    return $input;
  }

}


?>