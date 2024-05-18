<?php

class PhoneNumberAssignmentList extends DataList
{

  public function Setup()
  {

  }

  //--------------------------------------
  // List Search Functions
  //--------------------------------------

  public function GetByPhoneAndNumber($phone_id, $number_id)
  {
    foreach ($this->GetList() as &$assignment) {
      if ($assignment->phone_id == $phone_id and $assignment->number_id == $number_id)
        return $assignment;
    }

    return null;
  }

  //---------------------------------------
  // DB Load Functions
  //---------------------------------------

  public function LoadAll()
  {
    $query = "SELECT * FROM fuconfig.phone_number_assignment";

    return $this->LoadListFromQuery($query, "PhoneNumberAssignment");
  }



  public function LoadByNumberId($number_id)
  {
    $query = "SELECT * FROM fuconfig.phone_number_assignment
					WHERE number_id = :number_id
					ORDER BY display_order;";
    $parameters = array(":number_id" => $number_id);
    $classname = "PhoneNumberAssignment";

    $this->dataList = $this->LoadListFromQueryParameters($query, $classname, $parameters);
  }

  public function LoadByPhoneId($phone_id)
  {
    $query = "SELECT * FROM fuconfig.phone_number_assignment
					WHERE phone_id = :phone_id
					ORDER BY display_order;";
    $parameters = array(":phone_id" => $phone_id);
    $classname = "PhoneNumberAssignment";

    $this->dataList = $this->LoadListFromQueryParameters($query, $classname, $parameters);
  }

  public function LoadMarkedForDeletion()
  {
    $query = "SELECT * FROM fuconfig.phone_number_assignment
							WHERE todelete_assignment = 1";

    $this->LoadListFromQuery($query, "PhoneNumberAssignment");
  }

  public function LoadNotMarkedForDeletion()
  {
    $query = "SELECT * FROM fuconfig.phone_number_assignment
							WHERE todelete_assignment = 0";

    $this->LoadListFromQuery($query, "PhoneNumberAssignment");
  }

  public function LoadNotMarkedForDeletionByNumber($number_id)
  {
    $query = "SELECT * FROM fuconfig.phone_number_assignment
							WHERE todelete_assignment = 0 AND number_id = :number_id";
    $parameters = array(":number_id" => $number_id);

    $this->LoadListFromQueryParameters($query, "PhoneNumberAssignment", $parameters);
  }
}




?>