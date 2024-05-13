<?php

class NumberType extends DataClass
{

  public const LINE = 1;
  public const SPEEDDIAL = 2;
  public const CUSTOM = 3;
  public const SIP = 4;
  public const RANDOM = 5;

  protected function Setup()
  {
    //These 3 variables are used for generic DB update statements.
    $this->tableName = "fuconfig.number_types";
    //The Id field.
    $this->tableIdField = "number_type_id";
    //All table fields
    $this->tableFields = array(
      "number_type_id" => null,
      "number_type_name" => "",
      "is_default" => 0,
      "number_type_system_name" => "",
      "exclude_from_delete" => 0,
      "number_type_app_name" => ""
    );

    $this->CreateEmpty();
  }


}


?>