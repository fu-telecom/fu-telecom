<?php

class Extension extends DataClass
{

  public function Setup()
  {
    //These 3 variables are used for generic DB update statements.
    $this->tableName = "asteriskrealtime.extensions";
    //The Id field. 
    $this->tableIdField = "id";
    //All table fields
    $this->tableFields = array(
      "id" => null,
      "context" => "default",
      "exten" => null,
      "priority" => 1,
      "app" => "",
      "appdata" => ""
    );

    $this->CreateEmpty();
  }
}



?>