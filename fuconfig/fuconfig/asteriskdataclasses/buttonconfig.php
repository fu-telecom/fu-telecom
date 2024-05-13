<?php

class ButtonConfig extends DataClass
{

  public function Setup()
  {
    //These 3 variables are used for generic DB update statements.
    $this->tableName = "asteriskrealtime.buttonconfig";
    //The Id field.
    $this->tableIdField = "id";
    //All table fields
    $this->tableFields = array(
      "id" => null,
      "device" => null,
      "instance" => 0,
      "type" => "",
      "name" => "",
      "options" => "",
      "sccpdevice_id" => null
    );

    $this->CreateEmpty();
  }


}



?>