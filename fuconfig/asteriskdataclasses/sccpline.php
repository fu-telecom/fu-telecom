<?php

class SccpLine extends DataClass
{

  public function Setup()
  {
    //These 3 variables are used for generic DB update statements.
    $this->tableName = "asteriskrealtime.sccpline";
    //The Id field.
    $this->tableIdField = "id";
    //All table fields
    $this->tableFields = array(
      "id" => null,
      "pin" => "",
      "label" => "",
      "description" => "",
      "context" => "default",
      "incominglimit" => 2,
      "transfer" => "on",
      "mailbox" => "",
      "vmnum" => "2000",
      "cid_name" => "",
      "cid_num" => "",
      "trnsfvm" => "10",
      "musicclass" => "default",
      "echocancel" => "on",
      "silencesuppression" => "on",
      "callgroup" => null,
      "pickupgroup" => null,
      "amaflags" => null,
      "dnd" => "off",
      "setvar" => null,
      "name" => ""
    );

    $this->CreateEmpty();
  }

  public function LoadByNumber($number)
  {
    $query = "SELECT * FROM asteriskrealtime.sccpline WHERE id LIKE :id";
    $parameters = array(":id" => $number);
    return $this->LoadFromQueryParameters($query, $parameters);
  }
}



?>