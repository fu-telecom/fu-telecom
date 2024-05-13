<?php


class PhoneNumberAssignment extends DataClass
{

  private $phone;
  private $number;
  private $numberType;

  protected function Setup()
  {
    //These 3 variables are used for generic DB update statements.
    $this->tableName = "fuconfig.phone_number_assignment";
    //The Id field.
    $this->tableIdField = "phone_number_assignment_id";
    //All table fields
    $this->tableFields = array(
      "phone_number_assignment_id" => null,
      "number_id" => null,
      "phone_id" => null,
      "number_type_id" => null,
      "buttonconfig_id" => null,
      "todelete_assignment" => 0,
      "added_assignment" => 1,
      "password_index" => 1,
      "display_order" => 1
    );

    $this->CreateEmpty();
  }

  public function LoadByPhoneAndNumber($phone_id, $number_id)
  {
    $query = "SELECT * FROM phone_number_assignment WHERE phone_id = :phone_id AND number_id = :number_id";
    $parameters = array(":phone_id" => $phone_id, ":number_id" => $number_id);

    $this->LoadFromQueryParameters($query, $parameters);

    return $this;
  }


  //---------------------------------------
  // Related Table Data
  //---------------------------------------

  protected function LoadPhone()
  {
    if ($this->IsLoaded() == false) {
      $trace = debug_backtrace();
      trigger_error(
        'Data not loaded ' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_NOTICE
      );
      return null;
    }

    $this->phone = new Phone();
    return $this->phone->LoadFromDB($this->phone_id);
  }

  public function GetPhone()
  {
    return $this->phone ?? $this->LoadPhone();
  }

  protected function LoadNumber()
  {
    if ($this->IsLoaded() == false) {
      $trace = debug_backtrace();
      trigger_error(
        'Data not loaded ' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_NOTICE
      );
      return null;
    }

    $this->number = new Number();
    $this->number->LoadFromDB($this->number_id);

    return $this->number;
  }

  public function GetNumber()
  {
    return $this->number ?? $this->LoadNumber();
  }

  protected function LoadNumberType()
  {
    if ($this->IsLoaded() == false) {
      $trace = debug_backtrace();
      trigger_error(
        'Data not loaded ' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_NOTICE
      );
      return null;
    }

    $this->numberType = new NumberType();
    $this->numberType->LoadFromDB($this->number_type_id);

    return $this->numberType;
  }

  public function GetNumberType()
  {
    return $this->numberType ?? $this->LoadNumberType();
  }
}



?>