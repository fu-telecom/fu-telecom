<?php

class Voicemail extends DataClass
{

  public function Setup()
  {
    //These 3 variables are used for generic DB update statements.
    $this->tableName = "asteriskrealtime.voicemail";
    //The Id field.
    $this->tableIdField = "uniqueid";
    //All table fields
    $this->tableFields = array(
      "uniqueid" => null,
      "context" => "default",
      "mailbox" => "",
      "password" => "",
      "fullname" => ""
    );

    $this->CreateEmpty();
  }

  public function LoadByNumber($number)
  {
    $query = "SELECT * FROM asteriskrealtime.voicemail WHERE mailbox LIKE :mailbox";
    $parameters = array(":mailbox" => $number);

    return $this->LoadFromQueryParameters($query, $parameters);
  }
}



?>