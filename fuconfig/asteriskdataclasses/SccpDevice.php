<?php

class SccpDevice extends DataClass
{

  public function Setup()
  {
    //These 3 variables are used for generic DB update statements.
    $this->tableName = "asteriskrealtime.sccpdevice";
    //The Id field.
    $this->tableIdField = "id";
    //All table fields
    $this->tableFields = array(
      "id" => null,
      "type" => "",
      "addon" => null,
      "description" => "",
      "transfer" => "on",
      "cfwdall" => "on",
      "cfwdbusy" => "on",
      "dndFeature" => "off",
      "directrtp" => "",
      "earlyrtp" => "progress",
      "mwilamp" => "on",
      "mwioncall" => "off",
      "pickupexten" => "on",
      "pickupcontext" => "",
      "pickupmodeanswer" => "on",
      "private" => "off",
      "privacy" => "full",
      "conf_music_on_hold_class" => "default",
      "name" => null
    );

    $this->CreateEmpty();
  }

  public function LoadByDeviceName($deviceName)
  {
    $query = "SELECT * FROM asteriskrealtime.sccpdevice WHERE name LIKE :name";
    $parameters = array(":name" => $deviceName);

    $result = $this->LoadFromQueryParameters($query, $parameters);

    if ($result == null)
      return false;

    return true;
  }
}



?>