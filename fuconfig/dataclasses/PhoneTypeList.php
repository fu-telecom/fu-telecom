<?php

class PhoneTypeList extends DataList
{

  private static $staticPhoneTypeList = null;
  private static $staticDataList = null;

  public function Setup()
  {
  }

  public function GetList()
  {
    if (self::$staticPhoneTypeList == null) {
      self::LoadPhoneTypeList();
    }

    return self::$staticDataList;
  }

  public static function LoadPhoneTypeList()
  {
    if (self::$staticPhoneTypeList == null) {
      self::$staticPhoneTypeList = new PhoneTypeList();
      self::$staticDataList = self::$staticPhoneTypeList->LoadPhoneTypes();
    }

    return self::$staticPhoneTypeList;
  }


  public function LoadPhoneTypes()
  {
    $typeQuery = "SELECT * FROM fuconfig.phone_types;";
    $this->dataList = $this->LoadListFromQuery($typeQuery, "PhoneType");

    return $this->dataList;
  }

  public function GetPhoneType($type_id)
  {
    foreach ($this->GetList() as $index => $type) {
      if ($type->phone_type_id == $type_id) {
        //Type found, returning it. 
        return $type;
      }
    }

    //None found, returning null.
    return null;
  }
}

?>