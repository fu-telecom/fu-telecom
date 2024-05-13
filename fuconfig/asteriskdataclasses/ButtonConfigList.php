<?php

class ButtonConfigList extends DataList
{

  public function Setup()
  {

  }

  public function LoadByDeviceName($deviceName)
  {
    $query = "SELECT * FROM asteriskrealtime.buttonconfig WHERE device LIKE :device";
    $parameters = array(":device" => $deviceName);

    return $this->LoadListFromQueryParameters($query, "ButtonConfig", $parameters);
  }

  public function DeleteByDeviceName($deviceName)
  {
    $query = "DELETE FROM asteriskrealtime.buttonconfig
							WHERE device LIKE :device";
    $parameters = array(":device" => $deviceName);

    FUConfig::ExecuteParameterQuery($query, $parameters);
  }
}


?>