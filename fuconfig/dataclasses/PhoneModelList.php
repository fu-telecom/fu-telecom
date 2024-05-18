<?php

class PhoneModelList extends DataList
{

  private static $staticModelList = null;
  private static $staticDataList = null;

  public function Setup()
  {
  }

  public function GetList()
  {
    if (self::$staticModelList == null) {
      self::LoadPhoneModelList();
    }

    return self::$staticDataList;
  }

  public static function LoadPhoneModelList()
  {
    if (self::$staticDataList == null) {
      self::$staticModelList = new PhoneModelList;
      self::$staticDataList = self::$staticModelList->LoadPhoneModels();
    }

    return self::$staticModelList;
  }

  public function LoadPhoneModels()
  {
    $modelQuery = "SELECT * FROM fuconfig.phone_models;";
    $this->dataList = $this->LoadListFromQuery($modelQuery, "PhoneModel");

    return $this->dataList;
  }

  public function GetPhoneModel($model_id)
  {
    foreach ($this->GetList() as $index => $model) {
      if ($model->phone_model_id == $model_id) {
        //Model found, returning it. 
        return $model;
      }
    }

    //None found, returning null.
    return null;
  }
}

?>