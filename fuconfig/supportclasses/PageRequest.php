<?php

class PageRequest
{

  protected $pageRequest;
  protected $submitVars = array();
  protected $submitPage = "";

  //Constants for request type.
  const CREATE = 1;
  const REVIEW = 2;
  const UPDATE = 4;
  const DELETE = 8;
  const CUSTOM = 16;

  //Constructor: For current requests, can pass in $_REQUEST variable.
  public function __construct(&$request = null)
  {
    $this->pageRequest = $request;
  }

  public function Init($requestTypeConstant, $requestPage = "")
  {
    $this->SetRequestType($requestTypeConstant);
    $this->SetSubmitPage($requestPage);
  }

  //-------------------------------------------------
  //Form Action Functions
  //-------------------------------------------------

  //The actual page filename to process the request.
  public function SetSubmitPage($submitPage = "")
  {
    $this->submitPage = $submitPage;
  }

  //Complete form action -- will include page filename if set.
  public function GetRequestAction()
  {
    if ($this->IsValidFormAction() == false) {
      $trace = debug_backtrace();
      trigger_error(
        'Invalid Form Action -- Field Missing' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_NOTICE
      );
      return null;
    }

    $varUrl = "";

    foreach ($this->submitVars as $key => $value) {
      if (strlen($varUrl) == 0) {
        $varUrl .= "?";
      } else {
        $varUrl .= "&";
      }

      $varUrl .= $key . "=" . $value;
    }

    return $this->submitPage . $varUrl;
  }

  //Form Action Validation
  private function IsValidFormAction(): bool
  {
    if ($this->IsReviewRequest() or $this->IsUpdateRequest() or $this->IsDeleteRequest()) {
      return isset($this->submitVars['id']);
    } else {
      return true;
    }
  }

  //Returns true if the current request is a Create or is it set to Create? 
  public function IsCreateRequest(): bool
  {
    if (isset($this->pageRequest['create']) or isset($this->submitVars['create']))
      return true;
    else
      return false;
  }

  public function IsReviewRequest(): bool
  {
    if (isset($this->pageRequest['review']) or isset($this->submitVars['review']))
      return true;
    else
      return false;
  }

  public function IsUpdateRequest(): bool
  {
    if (isset($this->pageRequest['update']) or isset($this->submitVars['update']))
      return true;
    else
      return false;
  }

  public function IsDeleteRequest(): bool
  {
    if (isset($this->pageRequest['delete']) or isset($this->submitVars['delete']))
      return true;
    else
      return false;
  }

  //Set current request types.
  //NOTE: May need to clear other types or do some validation 
  //		to make sure there are not multiple types used simultaneously.
  public function SetCreate()
  {
    $this->submitVars['create'] = 1;
  }

  public function SetReview($id = null)
  {
    $this->submitVars['review'] = 1;
    $this->SetID($id);
  }

  public function SetUpdate($id = null)
  {
    $this->submitVars['update'] = 1;
    $this->SetID($id);
  }

  public function SetDelete($id = null)
  {
    $this->submitVars['delete'] = 1;
    $this->SetID($id);
  }

  //Bad and possibly unnecessary function. Either it should go or the other Set functions should.
  public function SetRequestType($requestTypeConstant, $id = null)
  {
    switch ($requestTypeConstant) {
      case PageRequest::CREATE:
        $this->SetCreate();
        break;
      case PageRequest::REVIEW:
        $this->SetReview($id);
        break;
      case PageRequest::UPDATE:
        $this->SetUpdate($id);
        break;
      case PageRequest::DELETE:
        $this->SetDelete($id);
        break;
      case PageRequest::CUSTOM:
        break;
      default:
        $trace = debug_backtrace();
        trigger_error(
          'Invalid Request Type for SetRequestType: ' . $requestTypeConstant .
          ' in ' . $trace[0]['file'] .
          ' on line ' . $trace[0]['line'],
          E_USER_NOTICE
        );
        return null;
    }
  }

  //ID field is required for review/update/delete.
  public function SetID($id)
  {
    $this->submitVars['id'] = $id;
  }

  public function GetID()
  {
    if (isset($this->pageRequest['id'])) {
      return $this->pageRequest['id'];
    } else if (isset($this->submitVars['id'])) {
      return $this->submitVars['id'];
    } else {
      $trace = debug_backtrace();
      trigger_error(
        'ID Field Not Set: ' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_NOTICE
      );
      return null;
    }
  }

  //General purpose variables for form action.
  public function SetVar($key, $value)
  {
    $this->submitVars[$key] = $value;
  }

  //Get value of general form action variables.
  public function GetVar($key)
  {
    if (isset($this->submitVars[$key]))
      return $this->submitVars[$key];
    else
      return null;
  }


  //----------------------------------------------------------------
  // Magic and other methods to get $_REQUEST data from PageRequest easily.
  // And also, this creates a way to talk to the internal data storage easily. 
  //----------------------------------------------------------------

  public function GetRequestData($name)
  {
    return $this->__get($name);
  }

  public function __get($name)
  {
    if (array_key_exists($name, $this->pageRequest)) {
      return $this->pageRequest[$name];
    }

    return null;
  }

  /**  As of PHP 5.1.0  */
  public function __isset($name)
  {
    return isset($this->pageRequest[$name]);
  }

  /**  As of PHP 5.1.0  */
  public function __unset($name)
  {
    unset($this->pageRequest[$name]);
  }


  //-------------------------------
  // Output Functionality
  //-------------------------------

  public function AsXML(&$parentXMLElement = null)
  {
    $xml = null;

    //Either used the supplied element or create its own with xml as its master.
    if ($parentXMLElement == null)
      $xml = new SimpleXMLElement('<xml/>');
    else
      $xml = $parentXMLElement;

    $this->AddAllRequestDataToXml($xml);

    return $xml;
  }

  private function AddAllRequestDataToXml(&$xml)
  {
    foreach ($this->pageRequest as $name => $value) {
      $xml->addChild($name, $value);
    }
  }



}



?>