<?php

class Router extends DataClass
{
  private $routerOrg = null;

  protected function Setup()
  {
    //These 3 variables are used for generic DB update statements.
    $this->tableName = "fuconfig.routers";
    //The Id field.
    $this->tableIdField = "router_id";
    //All table fields => default value
    $this->tableFields = array(
      "router_id" => null,
      "number" => null,
      "version" => 1,
      "channel_24" => 1,
      "channel_5" => 36,
      "org_id" => null,
      "router_is_deployed" => 0,
      "channel_24_current" => null,
      "channel_5_current" => null,
      "available" => 1,
      "enclosed" => "",
      "notes" => ""
    );

    $this->CreateEmpty();
  }

  public function GetIP()
  {
    return "172.16." . $this->number . ".1";
  }

  protected function LoadOrg()
  {
    if ($this->IsLoaded() == false) {
      trigger_error(
        'Phone data not loaded:' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_ERROR
      );
      return null;
    }

    $org = new Org();
    $org->LoadFromDB($this->org_id);

    return $org;
  }

  public function GetOrg()
  {
    if ($this->org_id == null)
      return null;

    return $this->LoadOrg();
  }
}

?>