<?php
include_once ('functions.php');
include_once ('defaults.php');

class FUConfig
{
  public static $config;
  public static $pdo;
  public static $defaults;

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
  }

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
}

FUConfig::init();









?>