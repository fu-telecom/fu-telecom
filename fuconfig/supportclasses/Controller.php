<?php

class Controller
{

  protected $data = array();

  protected $method = "";

  public function __construct()
  {

  }

  //-----------------------------------------------
  // Magic Methods for Data Access
  //-----------------------------------------------

  public function __get($name)
  {
    if (array_key_exists($name, $this->data)) {
      return $this->data[$name];
    }

    return null;
  }

  public function __set($name, $value)
  {
    $this->data[$name] = $value;

  }

  public function __isset($name)
  {
    return isset($this->data[$name]);
  }

  public function __unset($name)
  {
    unset($this->data[$name]);
  }

  //--------------------------------
  // Controller Action Type
  //
  // NOTE: A lot of this is copied from PageRequest, which I originally intended to use.
  //			...but... I was having issues, and I am out of time to make things pretty.
  //--------------------------------

  public function SetCreate()
  {
    $this->method = "create";
  }

  public function SetReview()
  {
    $this->method = "review";
  }

  public function SetUpdate()
  {
    $this->method = "update";
  }

  public function SetDelete()
  {
    $this->method = "delete";
  }

  public function IsCreateRequest(): bool
  {
    return $this->method == "create";
  }

  public function IsReviewRequest(): bool
  {
    return $this->method == "review";
  }

  public function IsUpdateRequest(): bool
  {
    return $this->method == "update";
  }

  public function IsDeleteRequest(): bool
  {
    return $this->method == "delete";
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

  protected function AddAllRequestDataToXml(&$xml)
  {
    foreach ($this->data as $name => $value) {
      $xml->addChild($name, $value);
    }
  }




}





?>