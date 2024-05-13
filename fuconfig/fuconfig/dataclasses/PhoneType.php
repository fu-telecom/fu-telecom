<?php


class PhoneType extends DataClass
{

  const SCCP = 1;
  const SIP = 2;

  protected function Setup()
  {
    //These 3 variables are used for generic DB update statements.
    $this->tableName = "fuconfig.phone_types";
    //The Id field.
    $this->tableIdField = "phone_type_id";
    //All table fields
    $this->tableFields = array(
      "phone_type_id" => null,
      "phone_type_name" => ""
    );

    $this->CreateEmpty();
  }




}


?>