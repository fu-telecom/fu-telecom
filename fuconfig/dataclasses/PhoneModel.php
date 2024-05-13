<?php


class PhoneModel extends DataClass
{

  protected function Setup()
  {
    //These 3 variables are used for generic DB update statements.
    $this->tableName = "fuconfig.phone_models";
    //The Id field.
    $this->tableIdField = "phone_model_id";
    //All table fields
    $this->tableFields = array(
      "phone_model_id" => null,
      "phone_model_name" => "",
      "phone_model_max_numbers" => 0,
      "phone_model_type_id" => 0,
      "xml_config_filename" => "SEPdefault.cnf.xml",
      "phone_model_system_name" => null,
      "add_button_list" => 0,
      "button_list_max" => 28,
      "page_size" => 14
    );

    $this->CreateEmpty();
  }


}


?>