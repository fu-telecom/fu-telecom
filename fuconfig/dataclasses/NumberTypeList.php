<?php

class NumberTypeList extends DataList
{

  private static $staticNumberTypeList = null;
  private static $staticDataList = null; //Likely not needed, but whatevs. 

  public function Setup()
  {
  }

  //Override GetList for static usage. 
  public function GetList()
  {
    if (self::$staticNumberTypeList == null) {
      self::LoadNumberTypeList();
    }

    return self::$staticDataList;
  }

  public static function LoadNumberTypeList()
  {
    if (self::$staticNumberTypeList == null) {
      self::$staticNumberTypeList = new NumberTypeList();
      self::$staticDataList = self::$staticNumberTypeList->LoadAll();
    }

    return NumberTypeList::$staticNumberTypeList;
  }

  public function LoadAll()
  {
    $query = "SELECT * FROM fuconfig.number_types;";
    $this->dataList = $this->LoadListFromQuery($query, "NumberType");

    return $this->dataList;
  }

  public function GetNumberTypeById($number_type_id)
  {
    foreach ($this->GetList() as $numberType) {
      if ($numberType->number_type_id == $number_type_id)
        return $numberType;
    }

    return null;
  }
}




?>