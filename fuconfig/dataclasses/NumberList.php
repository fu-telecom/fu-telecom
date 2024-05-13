<?php

class NumberList extends DataList
{

  public function Setup()
  {

  }

  public function LoadAll()
  {
    $query = "SELECT * FROM fuconfig.numbers";

    return $this->LoadListFromQuery($query, "Number");
  }

  public function LoadByPhoneAssignment($phone_id)
  {
    $query = "SELECT numbers.* FROM fuconfig.numbers
								INNER JOIN fuconfig.phone_number_assignment
									ON numbers.number_id = phone_number_assignment.number_id
								WHERE phone_number_assignment.phone_id = :phone_id
								ORDER BY display_order";
    $parameters = array(":phone_id" => $phone_id);

    return $this->LoadListFromQueryParameters($query, "Number", $parameters);
  }

  public function LoadByNumber($number)
  {
    $query = "SELECT * FROM numbers WHERE number LIKE :number";
    $parameters = array(":number" => $number);

    return $this->LoadListFromQueryParameters($query, "Number", $parameters);
  }

  public function LoadAllWithSort($sortfield)
  {
    $sortlist = "";

    if ($sortfield == "number")
      $sortlist = "number";
    else if ($sortfield == "callerid")
      $sortlist = "callerid";
    else {
      $trace = debug_backtrace();
      trigger_error(
        'Invalid Sort Field' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_ERROR
      );
      return false;
    }

    $query = "SELECT * FROM numbers ORDER BY " . $sortlist;

    return $this->LoadListFromQuery($query, "Number");
  }

  public function LoadMarkedForDeletion()
  {
    $query = "SELECT * FROM fuconfig.Numbers
							WHERE todelete_number = 1";

    $this->LoadListFromQuery($query, "Number");
  }

  public function LoadUnassignedNumbers()
  {
    $query = "SELECT numbers.* FROM fuconfig.numbers
							WHERE numbers.number_id IN (
								SELECT numbers.number_id FROM fuconfig.numbers
									LEFT JOIN fuconfig.phone_number_assignment
										ON numbers.number_id LIKE phone_number_assignment.number_id
									GROUP BY numbers.number_id
									HAVING COUNT(phone_number_assignment.number_id) = 0)";
    $this->LoadListFromQuery($query, "Number");
  }

  public function LoadByDirectory($phoneDirectory)
  {
    $query = "SELECT * FROM fuconfig.numbers
							WHERE directory_id = :directory_id
							ORDER BY callerid";
    $parameters = array(":directory_id" => $phoneDirectory->directory_id);

    $this->LoadListFromQueryParameters($query, "Number", $parameters);

  }

  public function LoadAllDisplayableLines()
  {
    $list = NumberType::LINE . "," . NumberType::SIP;
    $query = "SELECT * FROM fuconfig.numbers
				WHERE numbers.number_type_id IN (" . $list . ")";

    $this->LoadListFromQuery($query, "Number");
  }


}


?>