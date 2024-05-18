<?php

class Number extends DataClass
{

  private $numberType = null;
  private $phoneNumberAssignmentList = null;
  private $phoneDirectory = null;

  protected function Setup()
  {
    //These 3 variables are used for generic DB update statements.
    $this->tableName = "fuconfig.numbers";
    //The Id field.
    $this->tableIdField = "number_id";
    //All table fields
    $this->tableFields = array(
      "number_id" => null,
      "callerid" => null,
      "number" => null,
      "directory_id" => null,
      "number_type_id" => null,
      "todelete_number" => 0,
      "altered_number" => "0",
      "added_number" => 0,
      "sccpline_id" => null,
      "sip_user" => null,
      "sip_pass" => null,
      "sippeer_id" => null,
      "password_index" => 1
    );

    $this->CreateEmpty();
  }

  //---------------------------------------------
  // Related Table
  //---------------------------------------------

  protected function LoadDefaultNumberType()
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

    $this->numberType = NumberTypeList::LoadNumberTypeList()->GetNumberTypeById($this->number_type_id);

    return $this->numberType;
  }

  public function GetDefaultNumberType()
  {
    return $this->numberType ?? $this->LoadDefaultNumberType();
  }

  public function GetAssignmentByPhone($phone_id)
  {
    $assignment = $this->GetPhoneNumberAssignmentList()->GetByPhoneAndNumber($phone_id, $this->number_id);

    return $assignment;
  }

  public function GetNumberTypeByPhone($phone_id)
  {

    $assignment = $this->GetPhoneNumberAssignmentList()->GetByPhoneAndNumber($phone_id, $this->number_id);

    return $assignment->GetNumberType();
  }

  protected function LoadPhoneNumberAssignmentList()
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

    $this->phoneNumberAssignmentList = new PhoneNumberAssignmentList();
    $this->phoneNumberAssignmentList->LoadByNumberId($this->number_id);

    return $this->phoneNumberAssignmentList;
  }

  public function GetPhoneNumberAssignmentList()
  {
    return $this->phoneNumberAssignmentList ?? $this->LoadPhoneNumberAssignmentList();
  }

  protected function LoadPhoneDirectory()
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

    $this->phoneDirectory = new PhoneDirectory();
    $this->phoneDirectory->LoadFromDB($this->directory_id);

    return $this->phoneDirectory;
  }

  public function GetPhoneDirectory()
  {
    return $this->phoneDirectory ?? $this->LoadPhoneDirectory();
  }

  //-------------------------------------------
  // Special Data
  //-------------------------------------------

  public function GetAppNumber()
  {
    $app = $this->GetDefaultNumberType()->number_type_app_name;

    return $app . "/" . $this->number;
  }

}




?>