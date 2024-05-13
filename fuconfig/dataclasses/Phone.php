<?php

class Phone extends DataClass
{
  private $phoneOrg = null;
  private $phoneModel = null;
  private $phoneType = null;
  private $phoneInventory = null;

  /***********************************************
   * Load a specific phone.
   *
   * Use the static function LoadPhoneByID to load a phone
   * without having to know its phone_type_name ahead of time.
   *
   ***********************************************/

  public static function LoadPhoneByID($phone_id)
  {
    $returnPhone = null;

    $query = "SELECT * FROM fuconfig.phones WHERE phone_id = :phone_id";
    $parameters = array(":phone_id" => $phone_id);

    $data = FUConfig::ExecuteParameterQuery($query, $parameters);

    if ($data->rowCount() == 0)
      return null;

    $dataRow = $data->fetch(PDO::FETCH_ASSOC);

    if ($dataRow['phone_type_id'] == PhoneType::SCCP) {
      $returnPhone = new SccpPhone();

    } else if ($dataRow['phone_type_id'] == PhoneType::SIP) {
      $returnPhone = new SipPhone();

    } else {
      $trace = debug_backtrace();
      trigger_error(
        'Invalid phone_type_id via LoadPhoneByID() for phone_id: ' . $phone_id .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_ERROR
      );
      return null;
    }

    $returnPhone->LoadPhoneFromDataRow($dataRow);
    return $returnPhone;
  }

  public function LoadPhoneFromDataRow($dataRow)
  {
    $this->LoadFromDataRow($dataRow);

    $this->isnew = false;
  }

  //--------------------------------------
  // Setup Table Data and Custom Load Functions
  //--------------------------------------

  //Called by __construct -- sets up required info fo generic functions in DataClass
  protected function Setup()
  {
    //These 3 variables are used for generic DB update statements.
    $this->tableName = "fuconfig.phones";
    //The Id field.
    $this->tableIdField = "phone_id";
    //All table fields
    $this->tableFields = array(
      "phone_id" => null,
      "phone_type_id" => null,
      "phone_model_id" => null,
      "phone_org_id" => null,
      "phone_primary_number_id" => null,
      "phone_is_inventory" => 0,
      "phone_inventory_id" => null,
      "phone_serial" => "",
      "phone_is_deployed" => 0,
      "altered" => 0,
      "todelete_phone" => 0,
      "added" => 0,
      "errored" => 0,
      "sccpdevice_id" => null,
      "sip_username1" => null,
      "sip_password1" => null,
      "sip_username2" => null,
      "sip_password2" => null,
    );

    $this->CreateEmpty();
  }

  //Overrides parent LoadFromDataRow to get related tables/objects.
  public function LoadFromDataRow($dataRow)
  {
    parent::LoadFromDataRow($dataRow);

    $this->loaded = true;
    $this->isnew = false;

  }

  //Overrides parent to get related tables/objects.
  public function LoadFromDB($id)
  {
    $trace = debug_backtrace();
    trigger_error(
      'This function cant be used since only the children classes are instantiated. Use LoadPhoneByID() static function.' .
      ' in ' . $trace[0]['file'] .
      ' on line ' . $trace[0]['line'],
      E_USER_ERROR
    );
    return null;
    return null;
  }

  public function SaveToDB($include_id_in_insert = false)
  {
    if ($this->IsNew()) {
      $this->altered = 1;
      $this->added = 1;
    }

    parent::SaveToDB($include_id_in_insert);
  }

  //------------------------------------------------
  // Related Table Functionality
  //------------------------------------------------

  public function LoadRelated()
  {
    $this->LoadOrg();
    $this->LoadPhoneType();
    $this->LoadPhoneModel();
    $this->LoadPhoneInventory();
  }

  protected function LoadPhoneType()
  {
    if ($this->IsLoaded() == false) {
      trigger_error(
        'Phone data not loaded:' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_ERROR
      );
      return null;
    }

    $this->phoneType = PhoneTypeList::LoadPhoneTypeList()->GetPhoneType($this->phone_type_id);

    return $this->phoneType;
  }

  public function GetPhoneType()
  {
    return $this->phoneType ?? $this->LoadPhoneType();
  }

  protected function LoadPhoneModel()
  {
    if ($this->IsLoaded() == false) {
      trigger_error(
        'Phone data not loaded:' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_ERROR
      );
      return null;
    }

    $this->phoneModel = PhoneModelList::LoadPhoneModelList()->GetPhoneModel($this->phone_model_id);

    return $this->phoneModel;
  }

  public function GetPhoneModel()
  {
    return $this->phoneModel ?? $this->LoadPhoneModel();
  }

  protected function LoadOrg()
  {
    if ($this->IsLoaded() == false) {
      trigger_error(
        'Phone data not loaded:' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_ERROR
      );
      return null;
    }

    $this->phoneOrg = new Org();
    $this->phoneOrg->LoadFromDB($this->phone_org_id);

    return $this->phoneOrg;
  }

  public function GetOrg()
  {
    return $this->phoneOrg ?? $this->LoadOrg();
  }

  public function SetOrg($org)
  {
    $this->phoneOrg = $org;
    $this->phone_org_id = $org->org_id;
  }

  protected function LoadPhoneInventory()
  {
    if ($this->IsLoaded() == false) {
      trigger_error(
        'Phone data not loaded:' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_ERROR
      );
      return null;
    }

    $this->phoneInventory = new PhoneInventory();
    $this->phoneInventory->LoadFromDB($this->phone_inventory_id);

    return $this->phoneInventory;
  }

  public function GetPhoneInventory()
  {
    return $this->phoneInventory ?? $this->LoadPhoneInventory();
  }



}


?>