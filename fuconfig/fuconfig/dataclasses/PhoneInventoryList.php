<?php

class PhoneInventoryList extends DataList
{

  public $InventoryList = array();

  public function Setup()
  {

  }

  public function LoadAvailablePhoneInventory()
  {
    $inventoryListQuery = "SELECT * 
								FROM fuconfig.phone_inventory 
								WHERE phone_inventory_available = 1
								ORDER BY phone_inventory_tag;";

    $this->InventoryList = $this->LoadListFromQuery($inventoryListQuery, "PhoneInventory");
  }

  public function LoadAllPhoneInventory()
  {
    $inventoryListQuery = "SELECT * FROM fuconfig.phone_inventory ORDER BY phone_inventory_tag;";

    $this->InventoryList = $this->LoadListFromQuery($inventoryListQuery, "PhoneInventory", $this->InventoryList);
  }

  public function GetList()
  {
    return $this->InventoryList;
  }

}


?>