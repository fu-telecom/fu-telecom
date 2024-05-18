<?php

abstract class DataList
{
  protected $loaded = false;

  protected $dataList = array();

  public function __construct()
  {
    $this->Setup();
  }

  abstract public function Setup();

  public function GetList()
  {
    return $this->dataList;
  }

  public function GetCount()
  {
    if ($this->dataList == null)
      return 0;

    if (!isset($this->dataList))
      return 0;

    return count($this->dataList);
  }

  public function IsLoaded(): bool
  {
    return $loaded;
  }

  //-----------------------------------------------
  // Load Functions
  //-----------------------------------------------

  protected function LoadListFromQuery($query, $classname)
  {
    $data = FUConfig::ExecuteQuery($query);
    $dataList = array();

    while ($dataRow = $data->fetch()) {
      $newObj = new $classname;

      $newObj->LoadFromDataRow($dataRow);

      $dataList[] = $newObj;
    }

    $this->loaded = true;

    $this->dataList = $dataList;

    return $this->dataList;
  }

  //Overloaded version does parameter based query for sql injection safety.
  protected function LoadListFromQueryParameters($query, $classname, $parameters)
  {
    $count = 0;
    $dataList = array();

    $data = FUConfig::ExecuteParameterQuery($query, $parameters);

    while ($dataRow = $data->fetch()) {
      $newObj = new $classname;

      $newObj->LoadFromDataRow($dataRow);

      $dataList[] = $newObj;
      $count += 1;
    }

    if ($count > 0) {
      $this->loaded = true;
      $this->dataList = $dataList;
    } else {
      return null;
    }

    return $this->dataList;
  }

  //---------------------------------------
  // Iterative Operations
  //---------------------------------------

  public function DeleteAllFromDB()
  {
    foreach ($this->dataList as $item) {
      $item->DeleteFromDB();
    }
  }


}





?>