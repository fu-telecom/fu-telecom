<?php

//Table name is directories, but using PhoneDirectory instead as
//Directory appears to be a reserved word / already used class.
class PhoneDirectory extends DataClass
{
  protected function Setup()
  {
    //These 3 variables are used for generic DB update statements.
    $this->tableName = "fuconfig.directories";
    //The Id field.
    $this->tableIdField = "directory_id";
    //All table fields
    $this->tableFields = array(
      "directory_id" => null,
      "directory_name" => "",
      "directory_filename" => "",
      "default" => 0
    );

    $this->CreateEmpty();
  }
}



?>