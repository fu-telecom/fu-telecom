<?php
include_once ('includes/functions.php');
include_once ('includes/defaults.php');

class FUConfig
{
  public static $config;
  public static $pdo;
  public static $defaults;

  public const DEBUG_MODE = true;

  public static function init()
  {
    if (self::$config == NULL) {
      self::$config = new self();
    }

    return self::$config;
  }

  public function __construct()
  {
    self::$defaults = new Defaults();
    $this->LoadDB();
    $this->AutoLoadDataClasses();
    $this->AutoLoadSupportClasses();
    $this->AutoLoadAsteriskClasses();
    $this->AutoLoadRouterClasses();
  }

  public function AutoLoadDataClasses()
  {
    spl_autoload_register(function ($class) {
      $classFile = __DIR__ . '/dataclasses/' . $class . '.php';
      if (is_file($classFile))
        include $classFile;
    });
  }

  public function AutoLoadSupportClasses()
  {
    spl_autoload_register(function ($class) {
      $classFile = __DIR__ . '/supportclasses/' . $class . '.php';
      if (is_file($classFile))
        include $classFile;
    });
  }

  public function AutoLoadAsteriskClasses()
  {
    spl_autoload_register(function ($class) {
      $classFile = __DIR__ . '/asteriskrealtime/' . $class . '.php';
      if (is_file($classFile))
        include $classFile;
    });

    spl_autoload_register(function ($class) {
      $classFile = __DIR__ . '/asteriskdataclasses/' . $class . '.php';
      if (is_file($classFile))
        include $classFile;
    });
  }

  public function AutoLoadRouterClasses()
  {
    spl_autoload_register(function ($class) {
      $classFile = __DIR__ . '/routersupport/' . $class . '.php';
      if (is_file($classFile))
        include $classFile;
    });
  }


  //------------------------------------------
  // Database Support Functions
  //------------------------------------------

  //Initial DB setup. PDO is stored in static property.
  public function LoadDB()
  {
    $host = '127.0.0.1';
    $db = 'fuconfig';
    $user = 'fuconfig';
    $pass = 'fuconfig';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
      FUConfig::$pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
      throw new \PDOException($e->getMessage(), (int) $e->getCode());
    }

  }

  public static function ExecuteQuery($query)
  {
    try {
      $data = FUConfig::$pdo->query($query);
    } catch (PDOException $e) {
      if (FUConfig::DEBUG_MODE) {
        echo "<br /><b>Connection failure in ExecuteQuery.</b><br /><br />";
        echo "<b>Query: </b>" . $query . "<br /><br />";
        echo "<b>Error Message: </b>" . $e->getMessage() . "<br /><br />";
        echo "<b>Trace: </b>" . $e->getTraceAsString() . "<br /><br />";
      }

      throw $e;
    }


    return $data;
  }

  public static function ExecuteParameterQuery($query, $parameters)
  {
    try {
      $data = FUConfig::$pdo->prepare($query);
      $data->execute($parameters);
    } catch (PDOException $e) {
      echo "<br /><b>Connection failure in ExecuteParameterQuery.</b><br /><br />";
      echo "<b>Query: </b>" . $query . "<br /><br />";
      echo "<b>Parameters: </b>";
      var_dump($parameters);
      echo "<br /><br /><b>Error Message: </b>" . $e->getMessage() . "<br /><br />";
      echo "<b>Trace: </b>" . $e->getTraceAsString() . "<br /><br />";

      throw $e;
    }


    return $data;
  }
}

FUConfig::init();









?>