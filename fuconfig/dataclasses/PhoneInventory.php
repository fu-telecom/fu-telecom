<?php

class PhoneInventory extends DataClass
{
  //Related tables
  private $phoneModel;
  private $phoneType;

  //Related through the phone table.
  private $org = null;

  public function Setup()
  {
    //These 3 variables are used for generic DB update statements.
    $this->tableName = "fuconfig.phone_inventory";
    //The Id field.
    $this->tableIdField = "phone_inventory_id";
    //All table fields
    $this->tableFields = array(
      "phone_inventory_id" => null,
      "phone_inventory_tag" => "",
      "phone_inventory_serial" => "",
      "phone_inventory_type_id" => null,
      "phone_inventory_model_id" => null,
      "phone_inventory_available" => 1,
      "sip_username1" => null,
      "sip_password1" => null,
      "sip_username2" => null,
      "sip_password2" => null
    );

    $this->CreateEmpty();
  }

  public function LoadFromDataRow($dataRow)
  {
    parent::LoadFromDataRow($dataRow);

    $this->phoneModel = PhoneModelList::LoadPhoneModelList()->GetPhoneModel($this->phone_inventory_model_id);
    $this->phoneType = PhoneTypeList::LoadPhoneTypeList()->GetPhoneType($this->phone_inventory_type_id);

  }


  public function GetPhoneModel()
  {
    return $this->phoneModel;
  }

  public function GetPhoneType()
  {
    return $this->phoneType;
  }

  public function GetOrg()
  {
    return $this->org;
  }

  public function SetOrg($org)
  {
    $this->org = $org;
  }

  //------------------------------------------------
  // Inventory to Phone Functionality
  //------------------------------------------------

  public function CreatePhoneFromInventory()
  {
    //The new flag is set by default in DataClass for new objects.
    //This will also tell Phone to set the altered and added flags,
    //    when it is saved to the DB.
    $phone = null;

    if ($this->GetPhoneType()->phone_type_name == "SIP")
      $phone = new SipPhone();
    else if ($this->GetPhoneType()->phone_type_name == "SCCP")
      $phone = new SccpPhone();
    else {
      $trace = debug_backtrace();
      trigger_error(
        'Invalid Phone Type ' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_ERROR
      );
    }

    $this->TransferToPhone($phone);

    return $phone;

  }

  public function TransferToPhone($phone)
  {
    $phone->phone_inventory_id = $this->phone_inventory_id;
    $phone->phone_type_id = $this->phone_inventory_type_id;
    $phone->phone_model_id = $this->phone_inventory_model_id;
    $phone->phone_serial = $this->phone_inventory_serial;
    $phone->phone_is_inventory = 1;
    $phone->sip_username1 = $this->sip_username1;
    $phone->sip_password1 = $this->sip_password1;
    $phone->sip_username2 = $this->sip_username2;
    $phone->sip_password2 = $this->sip_password2;

    return $phone;
  }

}


?>