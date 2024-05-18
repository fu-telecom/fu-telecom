<?php

include_once ('FUConfig.php');

class Defaults
{

  public function __construct()
  {
    $this->SetDefaults();
    $this->DisplayErrors();
  }

  public function SetDefaults()
  {

    ini_set('allow_url_fopen', 'on');
  }

  public function DisplayErrors()
  {
    //Show errors on page for debug purposes.
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
  }


}



?>