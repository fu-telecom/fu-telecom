<?php

class ExtensionList extends DataList
{

  public function Setup()
  {

  }

  public function LoadByExten($exten)
  {
    $query = "SELECT * FROM asteriskrealtime.extensions
              WHERE exten LIKE :exten";
    $parameters = array(":exten" => $exten);

    $this->LoadListFromQueryParameters($query, "Extension", $parameters);
  }

}

?>