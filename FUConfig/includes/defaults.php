<?php

include_once('FUConfig.php');

class Defaults
{
	/*public static $setup;
	
	public static function init()
	{
		if (self::$setup == NULL)
            self::$setup = new self();

        return self::$setup;
	}*/
	
	public function __construct() {
		$this->SetDefaults();
		$this->DisplayErrors();
		//$this->AutoLoadClasses();
	}
	
	public function SetDefaults() {
		
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