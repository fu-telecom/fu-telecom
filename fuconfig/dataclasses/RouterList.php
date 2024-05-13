<?php

class RouterList extends DataList
{
  public function Setup()
  {

  }

  public function LoadAll()
  {
    $query = "SELECT * FROM fuconfig.routers ORDER BY number";

    return $this->LoadListFromQuery($query, "Router");
  }

  public function LoadByOrgId($org_id)
  {
    $query = "SELECT * FROM fuconfig.routers WHERE org_id = :org_id";
    $parameters = array(":org_id" => $org_id);

    return $this->LoadListFromQueryParameters($query, "Router", $parameters);
  }

  public function LoadAvailableRouterInventory()
  {
    $query = "SELECT * FROM fuconfig.routers
              WHERE available = 1 AND org_id is null";

    return $this->LoadListFromQuery($query, "Router");
  }
}

?>