<?php

class Org extends DataClass
{
  public $PhoneList = null;

  protected function Setup()
  {
    //These 3 variables are used for generic DB update statements.
    $this->tableName = "fuconfig.orgs";
    //The Id field. 
    $this->tableIdField = "org_id";
    //All table fields => default value
    $this->tableFields = array(
      "org_id" => null,
      "org_name" => "",
      "org_contactname" => "",
      "org_contactemail" => "",
      "org_contactphone" => ""
    );

    $this->CreateEmpty();

    //Can only be modified by class methods. 
    $this->SetReadOnly('org_id');


    //Not sure this is necessary. 
    //TODO: Remove if not needed.
  }



  /******************************************
   * Phones
   ******************************************/

  //Possibly redundant if I use public for $PhoneList.
  public function GetPhoneList()
  {
    return $this->PhoneList;
  }

  public function GetPhoneCount()
  {
    if ($this->IsLoaded() and $this->PhoneList->IsLoaded())
      return count($phoneList);
    else {
      $trace = debug_backtrace();
      trigger_error(
        'PhoneList not loaded in org: ' . $this->org_id .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_NOTICE
      );
      return null;
    }
  }

  //Load phones for this OrgID
  public function LoadPhones()
  {
    $this->PhoneList->LoadOrgPhones($this->org_id);
  }



  /******************************************
   * Implemented DataClass Abstract Methods
   ******************************************/

  public function LoadFromDB($id)
  {
    $query = "SELECT * FROM fuconfig.orgs WHERE org_id = :org_id";
    $this->LoadFromQueryParameters($query, array(':org_id' => $id));
  }

}







?>