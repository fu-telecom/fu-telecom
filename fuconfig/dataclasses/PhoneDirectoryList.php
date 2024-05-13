<?php

class PhoneDirectoryList extends DataList
{
  private static $staticPhoneDirectoryList = null;

  public function Setup()
  {

  }

  public function GetList()
  {
    return $this->dataList;
  }

  public static function LoadPhoneDirectoryList()
  {
    if (self::$staticPhoneDirectoryList == null) {
      self::$staticPhoneDirectoryList = new PhoneDirectoryList();
      self::$staticPhoneDirectoryList->LoadAll();
    }

    return self::$staticPhoneDirectoryList;
  }

  public function GetByDirectoryId($directory_id)
  {
    foreach ($this->dataList as $directory) {
      if ($directory->directory_id == $directory_id)
        return $directory;
    }
  }

  public function LoadAll()
  {
    $query = "SELECT * FROM fuconfig.directories";

    $this->dataList = $this->LoadListFromQuery($query, "PhoneDirectory");

    return $this->dataList;
  }

}




?>