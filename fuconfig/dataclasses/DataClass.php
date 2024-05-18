<?php

abstract class DataClass
{
  protected $data = array();
  protected $readonly = array();

  protected $tableName = "";
  protected $tableFields = array(); //Field names => default value
  protected $tableIdField = ""; //Primary key ID field.

  protected $loaded = false; //Is data loaded into class?
  protected $saved = false; //Are changes saved?
  protected $isnew = false;
  protected $isdeleted = false;

  //-----------------------------------------------------
  // Constructor / Startup Methods
  //-----------------------------------------------------

  //Loads the class and calls Setup, which is implemented by the subclasses.
  public function __construct()
  {
    $this->isnew = true;

    $this->Setup();
  }

  //Abstract method called by __construct()
  abstract protected function Setup();

  //-----------------------------------------------------
  // Data Conversion
  //-----------------------------------------------------

  //Returns an array based on the $tableFields array
  //with the corresponding data from $data.
  public function GetTableData()
  {
    $tableData = array();

    foreach ($this->tableFields as $field => $value) {
      if (isset($this->data[$field]))
        $tableData[$field] = $this->data[$field];
      else
        $tableData[$field] = null;
    }

    return $tableData;
  }


  //-----------------------------------------------------
  // Data Management Methods
  //-----------------------------------------------------

  //Abstract method to create a blank data class, setting class defaults.
  public function CreateEmpty()
  {
    foreach ($this->tableFields as $field => $value) {
      $this->data[$field] = $value;
    }
  }

  //Generic load from DB function. Override for custom implementations.
  public function LoadFromDB($id)
  {
    $this->data[$this->tableIdField] = $id;
    $this->GenericSelectDB();
  }

  //Generic SaveToDB function, uses generic functions for INSERT and UPDATE.
  //Override to do something different.
  public function SaveToDB($include_id_in_insert = false)
  {
    if ($this->IsNew()) {
      $this->GenericInsertDB($include_id_in_insert);
    } else {
      $this->GenericUpdateDB();
    }
  }

  public function ExecuteQueryParameters($query, $parameters, $getLastInsertIdField = false)
  {
    FUConfig::ExecuteParameterQuery($query, $parameters);

    if ($getLastInsertIdField) {
      $this->data[$this->tableIdField] = FUConfig::$pdo->lastInsertId();
    }
  }

  public function DeleteFromDB()
  {
    $this->GenericDeleteDB();
  }

  public function LoadFromDataRow($dataRow)
  {
    $this->data = $dataRow;

    $this->loaded = true;
  }

  public function LoadFromQuery($query)
  {
    $data = FUConfig::$pdo->query($query);
    $dataRow = $data->fetch(PDO::FETCH_ASSOC);

    //NOTE: This is a bit of a kludge. The instantiated object of this won't
    //be null, unless this function is used for assignment.
    if ($dataRow == false) {
      return null;
    }

    //Loads datarow into $data and sets loaded flag.
    $this->LoadFromDataRow($dataRow);

    //Loaded from DB, can't be new.
    $this->isnew = false;

    return $this;
  }

  public function LoadFromQueryParameters($query, $parameters)
  {
    $data = FUConfig::$pdo->prepare($query);
    $data->execute($parameters);
    $dataRow = $data->fetch(PDO::FETCH_ASSOC);

    //NOTE: This is a bit of a kludge. The instantiated object of this won't
    //be null, unless this function is used for assignment.
    if ($dataRow == false) {
      return null;
    }

    //Loads datarow into $data and sets loaded flag.
    $this->LoadFromDataRow($dataRow);

    //Loaded from DB, can't be new.
    $this->isnew = false;

    return $this;
  }

  public function LoadFromPageRequest($pageRequest)
  {

    if (!$pageRequest->IsCreateRequest()) {
      $this->data[$this->tableIdField] = $pageRequest->GetID();
      $this->isnew = false;
    } else {
      $this->isnew = true;
    }

    foreach ($this->tableFields as $field => $value) {

      if ($pageRequest->__isset($field)) {
        $this->data[$field] = $pageRequest->GetRequestData($field);
      }
    }

    $this->loaded = true;
  }


  //----------------------------------------------
  // Generic Update Queries and Query Builders
  //----------------------------------------------

  //Set all the required table info fields.
  protected function SetTableInfo($tableName, $tableFields, $tableIdField)
  {
    $this->tableName = $tableName;
    $this->tableFields = $tableFields;
    $this->tableIdField = $tableIdField;
  }

  //UPDATE DB
  protected function GenericUpdateDB()
  {
    if ($this->IsTableInfoLoaded() == false) {
      $trace = debug_backtrace();
      trigger_error(
        'Table info not loaded -- generic update failed: ' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_ERROR
      );
      return null;
    }

    $query = $this->GetUpdateStatement();
    $parameters = $this->GetAllParameters(true);

    $this->ExecuteQueryParameters($query, $parameters);

    $this->saved = true;
    $this->isnew = false;
  }

  //INSERT INTO DB
  protected function GenericInsertDB($include_id_in_insert = false)
  {
    if ($this->IsTableInfoLoaded() == false) {
      $trace = debug_backtrace();
      trigger_error(
        'Table info not loaded -- generic create failed: ' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_ERROR
      );
      return null;
    }

    $query = $this->GetInsertStatement($include_id_in_insert);
    $parameters = $this->GetAllParameters($include_id_in_insert);

    $getLastInsertId = ($include_id_in_insert == false);

    $this->ExecuteQueryParameters($query, $parameters, $getLastInsertId);

    //Set ID Field of newly inserted object.

    $this->saved = true;
    $this->isnew = false;
    $this->isdeleted = false;
  }

  //SELECT FROM DB
  protected function GenericSelectDB()
  {
    $query = $this->GetSelectStatement();
    $parameters = $this->GetIDFieldParameters(); //Only need 1 parameter, the ID field.

    $this->LoadFromQueryParameters($query, $parameters);

    $this->isnew = false;
    $this->isdeleted = false;
    $this->saved = true;
  }

  //DELETE FROM DB
  protected function GenericDeleteDB()
  {
    $query = $this->GetDeleteStatement();
    $parameters = $this->GetIDFieldParameters();

    $this->ExecuteQueryParameters($query, $parameters);

    $this->isnew = false;
    $this->loaded = false;
    $this->isdeleted = true;
    $this->saved = false;
  }

  //Validator to make sure all table info is loaded.
  //Otherwise the generic functions will fail badly.
  protected function IsTableInfoLoaded(): bool
  {
    if (strlen($this->tableName) > 0 and count($this->tableFields) > 0 and strlen($this->tableIdField) > 0)
      return true;
    else
      return false;
  }

  //Returns the list of fields as comma separated string.
  private function GetFieldList($includeId = false): string
  {
    $fieldList = "";

    foreach ($this->tableFields as $field => $value) {
      if ($includeId == false and $this->tableIdField == $field) {
        continue;
      }

      if (strlen($fieldList) > 0)
        $fieldList .= ", ";

      $fieldList .= "`" . $field . "`";
    }

    return $fieldList;
  }

  //Gets the actual parameterized SELECT query string.
  private function GetSelectStatement(): string
  {
    $statement = "SELECT * FROM " . $this->tableName .
      " WHERE " . $this->tableIdField . " = :" . $this->tableIdField . ";";

    return $statement;
  }

  //Gets the actual parameterized INSERT query string.
  private function GetInsertStatement($include_id_in_insert): string
  {
    $fieldList = $this->GetFieldList($include_id_in_insert);
    $statement = "INSERT INTO " . $this->tableName . " (" . $fieldList . ") VALUES (";
    $paramList = "";

    foreach ($this->tableFields as $field => $value) {
      //Skip ID Field.
      if ($field == $this->tableIdField and $include_id_in_insert == false)
        continue;

      if (strlen($paramList) > 0)
        $paramList .= ", ";

      $paramList .= ":" . $field;
    }

    $statement .= $paramList . ");";

    return $statement;
  }

  //Gets the actual parameterized UPDATE query string.
  private function GetUpdateStatement(): string
  {
    $statement = "UPDATE " . $this->tableName . " SET ";
    $fieldList = "";

    foreach ($this->tableFields as $field => $value) {
      //Skip ID Field.
      if ($field == $this->tableIdField)
        continue;

      if (strlen($fieldList) > 0)
        $fieldList .= ", ";

      $fieldList .= "`" . $field . "` = :" . $field;
    }

    $statement .= $fieldList . " WHERE " . $this->tableIdField . " = :" . $this->tableIdField . ";";

    return $statement;
  }

  private function GetDeleteStatement(): string
  {
    $statement = "DELETE FROM " . $this->tableName .
      " WHERE " . $this->tableIdField . " = :" . $this->tableIdField . ";";

    return $statement;
  }

  //QUERY PARAMS: Get all fields as an array that can be used for query parameters.
  public function GetAllParameters($includeId = false): array
  {
    $parameters = array();

    foreach ($this->tableFields as $field => $value) {
      if ($includeId == false and $this->tableIdField == $field)
        continue;

      $parameters[":" . $field] = $this->data[$field];
    }

    return $parameters;
  }

  public function GetIDFieldParameters(): array
  {
    return array(":" . $this->tableIdField => $this->data[$this->tableIdField]);
  }

  //-----------------
  // Flags / Markers
  //-----------------

  public function IsLoaded(): bool
  {
    return $this->loaded;
  }

  public function IsSaved(): bool
  {
    return $this->saved;
  }

  public function IsNew(): bool
  {
    return $this->isnew;
  }

  public function IsDeleted(): bool
  {
    return $this->isdeleted;
  }


  /***************************************
   * Data Parameter Management
   ***************************************/

  //Sets properties that can only be written by the class itself.
  protected function SetReadOnly($name)
  {
    $this->readonly[$name] = $name;
  }

  public function GetDataAsArray()
  {
    return $this->data;
  }

  public function __set($name, $value)
  {
    if (isset($this->readonly[$name])) {
      $trace = debug_backtrace();
      trigger_error(
        'Read-only property via __set(): ' . $name .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_NOTICE
      );
      return null;
    }

    $this->data[$name] = $value;
    $this->saved = false; //Unset saved flag after data is modified.
  }

  public function __get($name)
  {
    if (array_key_exists($name, $this->data)) {
      return $this->data[$name];
    }

    $trace = debug_backtrace();
    trigger_error(
      'Undefined property via __get(): ' . $name .
      ' in ' . $trace[0]['file'] .
      ' on line ' . $trace[0]['line'],
      E_USER_NOTICE
    );
    return null;
  }

  /**  As of PHP 5.1.0  */
  public function __isset($name)
  {
    return isset($this->data[$name]);
  }

  /**  As of PHP 5.1.0  */
  public function __unset($name)
  {
    unset($this->data[$name]);
  }


  //-------------------------------
  // Output Functionality
  //-------------------------------

  public function AsXML(&$parentXMLElement = null)
  {
    $xml = null;

    //Either used the supplied element or create its own with xml as its master.
    if ($parentXMLElement == null)
      $xml = new SimpleXMLElement('<xml/>');
    else
      $xml = $parentXMLElement;

    $this->AddAllDataToXml($xml);

    return $xml;
  }

  private function AddAllDataToXml(&$xml)
  {
    foreach ($this->data as $name => $value) {
      $xml->addChild($name, $value);
    }
  }



}


?>