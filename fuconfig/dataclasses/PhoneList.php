<?php


class PhoneList extends DataList
{

  private $parentOrg = null;

  public function Setup()
  {

  }

  public function LoadAllPhones()
  {
    $query = "SELECT * FROM fuconfig.phones";
    $data = FUConfig::ExecuteQuery($query);

    $this->LoadFromDataRowArray($data);
  }

  //Load phones based on org_id.
  public function LoadOrgPhones($org_id)
  {
    $query = "SELECT * FROM fuconfig.phones
					WHERE phone_org_id = :org_id";
    $parameters = array(':org_id' => $org_id);

    $data = FUConfig::ExecuteParameterQuery($query, $parameters);

    while ($dataRow = $data->fetch()) {
      $this->dataList[] = $this->LoadPhone($dataRow);
    }

    $this->parentOrg = new Org();
    $this->parentOrg->LoadFromDB($org_id);

    $this->loaded = true;
  }

  //Determines whether a phone is SIP or SCCP and creates the appropriate subclass of Phone.
  private function LoadPhone($dataRow)
  {
    $phoneType = PhoneTypeList::LoadPhoneTypeList()->GetPhoneType($dataRow['phone_type_id']);
    $newPhone = null;

    if ($phoneType->phone_type_name == 'SCCP') {
      $newPhone = new SccpPhone();
    } else if ($phoneType->phone_type_name == 'SIP') {
      $newPhone = new SipPhone();
    } else {
      $trace = debug_backtrace();
      trigger_error(
        'Invalid phone_type_id via LoadPhone() for phone_id: ' . $dataRow['phone_id'] .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_ERROR
      );
    }
    $newPhone->LoadFromDataRow($dataRow);

    return $newPhone;
  }

  public function LoadModifiedPhones()
  {
    $query = "SELECT * FROM fuconfig.phones
							WHERE altered = 1 OR added = 1 OR todelete_phone = 1";
    $data = FUConfig::ExecuteQuery($query);

    $this->LoadFromDataRowArray($data);
  }

  public function LoadMarkedForDeletion()
  {
    $query = "SELECT * FROM fuconfig.phones
							WHERE todelete_phone = 1";
    $data = FUConfig::ExecuteQuery($query);

    $this->LoadFromDataRowArray($data);
  }



  public function FindByInventoryId($inventoryId)
  {
    foreach ($this->GetList() as &$phone) {
      if ($phone->phone_inventory_id == $inventoryId) {
        return $phone;
      }
    }

    return null;
  }

  private function LoadFromDataRowArray($data)
  {
    while ($dataRow = $data->fetch()) {
      $this->dataList[] = $this->LoadPhone($dataRow);
    }

    $this->loaded = true;
  }

  //-----------------------------------
  // Related Tables
  //-----------------------------------

  public function SetParentOrg($org)
  {
    $this->parentOrg = $org;
  }

  public function GetParentOrg()
  {
    return $this->parentOrg;
  }


}




?>