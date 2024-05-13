<?php

class ButtonRequest extends PageRequest
{

  protected $buttonLabel;

  //Colors of Bootstrap
  const PRIMARY = 1;
  const SECONDARY = 2;
  const SUCCESS = 4;
  const DANGER = 8;
  const WARNING = 16;
  const INFO = 32;

  protected $displayColor = 0;

  //Types of Bootstrap
  const BTN = 1;
  const TEXT = 2;

  protected $displayType = 0;

  protected $onClick = "";
  protected $cssClasses = array();

  public function Init($requestTypeConstant, $displayColor = ButtonRequest::PRIMARY, $displayType = ButtonRequest::BTN, $requestPage = "", $buttonLabel = "")
  {
    parent::Init($requestTypeConstant, $requestPage);

    $this->SetButtonLabel($buttonLabel);
    $this->SetDisplayColor($displayColor);
    $this->SetDisplayType($displayType);
  }

  public function InitCreate($requestPage, $buttonLabel)
  {
    $this->Init(PageRequest::CREATE, ButtonRequest::PRIMARY, ButtonRequest::BTN, $requestPage, $buttonLabel);
  }

  public function InitUpdate($requestPage, $buttonLabel)
  {
    $this->Init(PageRequest::UPDATE, ButtonRequest::PRIMARY, ButtonRequest::BTN, $requestPage, $buttonLabel);
  }

  public function InitDelete($requestPage, $buttonLabel)
  {
    $this->Init(PageRequest::DELETE, ButtonRequest::DANGER, ButtonRequest::BTN, $requestPage, $buttonLabel);
  }

  public function GetSubmitButton()
  {
    $button = "<input type=\"submit\" value=\"" . $this->buttonLabel .
      "\" class=\"" . $this->GetCSS() . "\" ";

    if (strlen($this->onClick) > 0) {
      $button .= "onclick=\"" . $this->onClick . "\"";
    }

    $button .= "/>";

    return $button;
  }

  public function GetAnchorButtonHTML()
  {
    $button = "<a class=\"" . $this->GetCSS() . "\"" .
      " href=\"" . $this->GetRequestAction() . "\" role=\"button\"";

    if (strlen($this->onClick) > 0) {
      $button .= "onclick=\"" . $this->onClick . "\"";
    }

    $button .= ">" . $this->buttonLabel . "</a>";

    return $button;
  }

  public function SetButtonLabel($label)
  {
    $this->buttonLabel = $label;
  }

  public function GetButtonLabel()
  {
    return $this->buttonLabel;
  }

  public function SetOnClick($onClick)
  {
    $this->onClick = $onClick;
  }

  //----------------------------------------------
  //CSS Display Helpers for Request
  //----------------------------------------------

  //Requires one of the color constants defined at the top.
  public function SetDisplayColor($colorConstant)
  {
    $this->displayColor = $colorConstant;
  }

  public function GetDisplayCSS()
  {
    $prefix = "";
    $color = "";

    switch ($this->displayType) {
      case ButtonRequest::BTN:
        $prefix = "btn";
        break;
      case ButtonRequest::TEXT:
        $prefix = "text";
        break;
      default:
        $trace = debug_backtrace();
        trigger_error(
          'Invalid type for displayType: ' . $this->displayType .
          ' in ' . $trace[0]['file'] .
          ' on line ' . $trace[0]['line'],
          E_USER_NOTICE
        );
        return null;
    }

    switch ($this->displayColor) {
      case ButtonRequest::PRIMARY:
        $color = "primary";
        break;
      case ButtonRequest::SECONDARY:
        $color = "secondary";
        break;
      case ButtonRequest::SUCCESS:
        $color = "success";
        break;
      case ButtonRequest::DANGER:
        $color = "danger";
        break;
      case ButtonRequest::WARNING:
        $color = "warning";
        break;
      case ButtonRequest::INFO:
        $color = "info";
        break;
      default:
        $trace = debug_backtrace();
        trigger_error(
          'Invalid color for displayColor: ' . $this->displayColor .
          ' in ' . $trace[0]['file'] .
          ' on line ' . $trace[0]['line'],
          E_USER_NOTICE
        );
        return null;
    }

    return $prefix . " " . $prefix . "-" . $color;
  }

  //Requires one of the type constants defined at the top.
  public function SetDisplayType($typeConstant)
  {
    $this->displayType = $typeConstant;
  }

  public function AddCSSClass($class)
  {
    $this->cssClasses[] = $class;
  }

  public function GetCSS()
  {
    $css = "";

    $css .= $this->GetDisplayCSS();

    foreach ($this->cssClasses as $class) {
      if (strlen($css) > 0)
        $css .= " ";

      $css .= $class;
    }

    return $css;
  }
}


?>