<?php

class SipPeer extends DataClass
{

  public function Setup()
  {
    //These 3 variables are used for generic DB update statements.
    $this->tableName = "asteriskrealtime.sippeers";
    //The Id field.
    $this->tableIdField = "id";
    //All table fields
    $this->tableFields = array(
      "id" => null,
      "name" => "",
      "ipaddr" => null,
      "port" => null,
      "regseconds" => null,
      "host" => "dynamic",
      "type" => "friend",
      "context" => "default",
      "permit" => null,
      "disallow" => "all",
      "allow" => "ulaw",
      "callcounter" => "yes",
      "secret" => null,
      "callerid" => null,
      "vmexten" => null,
      "cid_number" => null
    );

    $this->CreateEmpty();
  }

  public function LoadByName($name)
  {
    $query = "SELECT * FROM asteriskrealtime.sippeers WHERE name LIKE :name";
    $parameters = array(":name" => $name);

    return $this->LoadFromQueryParameters($query, $parameters);
  }
}



?>