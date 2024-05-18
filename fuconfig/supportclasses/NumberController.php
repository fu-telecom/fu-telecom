<?php

class NumberController extends Controller
{

  private $setupComplete = false;
  private $pageRequest = null;

  //------------------------------------------------
  // Setup Functionality
  //------------------------------------------------

  //TODO: This is really really messy. It should accept the PageRequest and just incorporate it automagically.
  public function Setup(
    $phone_id = null,
    $callerid = null,
    $number = null,
    $directory_id = null,
    $number_type_id = null,
    $isnew = false,
    $number_id = null,
    $assignment_id = null,
    $password_index = 1
  ) {

    $this->phone_id = $phone_id;
    $this->callerid = $callerid;
    $this->number = $number;
    $this->directory_id = $directory_id;
    $this->number_type_id = $number_type_id;
    $this->password_index = $password_index;

    //Possibly unused, depending on request type.
    $this->number_id = $number_id;
    $this->assignment_id = $assignment_id;

    $this->added = 0;
    $this->already_exists = 0;
    $this->format_problem = 0;

    $this->toomany_numbers = 0;
    $this->total_numbers = 0;
    $this->max_numbers = 0;

    $this->duplicate_violation = 0;

    $this->validate = 1;
    $this->edited = 0;

    $this->isnew = $isnew;

    $this->setupComplete = true;

  }

  public function SetupFromPageRequest($pageRequest)
  {
    //Should have used this one line before, instead of having that big
    //nasty Setup function. Blah. Oh well.
    $this->pageRequest = $pageRequest;

    $this->Setup(
      $pageRequest->phone_id,
      $pageRequest->callerid,
      $pageRequest->number,
      $pageRequest->directory_id,
      $pageRequest->number_type_id,
      $pageRequest->isnew,
      $pageRequest->number_id,
      $pageRequest->phone_number_assignment_id,
      $pageRequest->password_index
    );

    if ($pageRequest->IsCreateRequest()) {
      $this->SetCreate();
    } else if ($pageRequest->IsUpdateRequest()) {
      $this->SetUpdate();

    } else if ($pageRequest->IsDeleteRequest()) {
      $this->SetDelete();
    } else {

    }
  }

  //-----------------------------------------------------
  // Validation: Why can't I be validated this easily?
  //-----------------------------------------------------

  public function Validate()
  {
    if ($this->setupComplete == false) {
      $trace = debug_backtrace();
      trigger_error(
        'Must first run the Setup function to use this class ' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_ERROR
      );
      return null;
    }

    if ($this->IsCreateRequest()) {

      return $this->ValidateCreate();

    } else if ($this->IsUpdateRequest()) {

      return $this->ValidateUpdate();

    } else if ($this->IsDeleteRequest()) {

      return $this->ValidateDelete();

    } else {
      $trace = debug_backtrace();
      trigger_error(
        'Invalid Request Type For This Class' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_ERROR
      );
      return null;
    }
  }

  private function ValidateCreate()
  {
    if ($this->isnew == true and $this->InputIsValid() == false) {
      return false;
    }

    if ($this->isnew == true and $this->NumberAlreadyExists() == true) {
      return false;
    }

    if ($this->isnew == false and $this->IdFieldsAreValid() == false) {
      return false;
    }

    if ($this->PhoneLineMaxReached()) {
      return false;
    }

    return true;
  }

  private function ValidateUpdate()
  {
    if ($this->IdFieldsAreValid() == false) {
      return false;
    }

    return true;
  }

  private function ValidateDelete()
  {
    //Make sure ID Fields are valid, as they are required everywhere, and a failure here indicates a
    //fundamental programming issue.
    if ($this->IdFieldsAreValid() == false) {
      $trace = debug_backtrace();
      trigger_error(
        'Invalid request data or Setup not run' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_ERROR
      );
      return null;
    }

    return true;
  }

  private function IdFieldsAreValid(): bool
  {
    echo "Phone_ID LEngth: " . strlen($this->phone_id) . "<br>";
    echo "Phone_ID LEngth: " . strlen($this->number_id) . "<br>";
    if (strlen($this->phone_id) == 0 or strlen($this->number_id) == 0) {
      $this->format_problem = 1;

      return false;
    }

    return true;
  }

  //Check to see if callerid and number input fields are valid.
  private function InputIsValid(): bool
  {
    if (strlen($this->number) == 0 or strlen($this->callerid) == 0 or !is_numeric($this->number)) {
      //Not valid!
      $this->format_problem = 1; //Indicate a format error.
      echo "Bad Input<br />";
      return false;
    }

    return true;
  }

  private function NumberAlreadyExists(): bool
  {
    $numberList = new NumberList();
    $numberList->LoadByNumber($this->number);

    if ($numberList->GetCount() >= 1) {
      $this->already_exists = 1; //Indicate already exists error.
      echo "Number Exists<br />";
      return true;
    }

    return false;
  }

  //TODO: Check to see if this is working right. May need to use assignments.
  private function PhoneLineMaxReached(): bool
  {
    $phone = Phone::LoadPhoneByID($this->phone_id);

    $this->max_numbers = $phone->GetPhoneModel()->phone_model_max_numbers;

    $numberList = new NumberList();
    $numberList->LoadByPhoneAssignment($this->phone_id);
    $this->total_numbers = $numberList->GetCount();

    if ($this->total_numbers >= $this->max_numbers) {
      $this->toomany_numbers = 1;
      echo "Too Many Numbers.<br />";
      return true;
    }

    return false;
  }

  //----------------------------------------------------
  // Request Processing
  //----------------------------------------------------

  public function ProcessRequest()
  {
    if ($this->setupComplete == false) {
      $trace = debug_backtrace();
      trigger_error(
        'Must first run the Setup function to use this class ' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_ERROR
      );
      return false;
    }

    echo "Validating.<Br />";

    if ($this->Validate() == false) {
      //Request is invalid -- can't process.
      return false;
    }
    echo "Validated.<br>";

    if ($this->IsCreateRequest()) {
      echo "Create<br>";
      try {
        $this->ProcessCreateRequest();
      } catch (PDOException $e) {
        if ($e->GetCode() == 23000) {
          $this->duplicate_violation = 1;
          $this->validate = 0;

          return false;
        } else {
          throw $e;
        }
      }

      return true;

    } else if ($this->IsUpdateRequest()) {
      $this->ProcessUpdateRequest();

      return true;

    } else if ($this->IsDeleteRequest()) {
      $this->ProcessDeleteRequest();

      return true;
    } else {
      $trace = debug_backtrace();
      trigger_error(
        'Invalid Request Type For This Class' .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_ERROR
      );
      return false;
    }
  }

  private function ProcessCreateRequest()
  {
    echo "ProcessCreateRequest<br/>";

    //If this is new.
    if ($this->isnew == true) {
      echo "This is new<Br>";
      $numberObj = new Number();
      $numberObj->CreateEmpty();
      $this->SaveNumberData($numberObj);

      $this->number_id = $numberObj->number_id;
    }

    $assignment = new PhoneNumberAssignment();
    $assignment->CreateEmpty();
    $assignment->added_assignment = 1; //Flag as added. Possibly superfluous.
    if ($this->isnew == null or $this->isnew == false) {
      $assignmentList = new PhoneNumberAssignmentList();
      $assignmentList->LoadByPhoneId($this->phone_id);
      $assignment->display_order = $assignmentList->GetCount() + 1;
    }
    $this->SaveAssignmentData($assignment);

    //Set altered flag on the phone.
    $this->SetPhoneAltered();

    //Set altered flag on number.
    $this->SetNumberAdded();

    //Mark as added.
    $this->added = 1;

    //Data to send back
    $this->phone_number_assignment_id = $assignment->phone_number_assignment_id;

    echo "ProcessCreateRequest Complete<br>";
  }

  private function ProcessUpdateRequest()
  {

    $numberObj = new Number();
    $numberObj->LoadFromDB($this->number_id);
    $numberObj->altered_number = 1; //Flag as altered number. Possibly superfluous.
    $this->SaveNumberData($numberObj);

    $assignment = new PhoneNumberAssignment();
    $assignment->LoadByPhoneAndNumber($this->phone_id, $this->number_id);
    $assignment->display_order = $this->pageRequest->display_order;
    $this->SaveAssignmentData($assignment);

    //Set altered flag on the phone.
    $this->SetPhoneAltered();
    $this->SetNumberAltered();

    //Variable to indicate which phone_id to be reloaded if AJAX call, probably superfluous.
    $this->updateList = $this->phone_id;

    //Mark as edited.
    $this->edited = 1;

  }

  //Deletes are a little more complicted.
  //We can't delete it for real unless it has been deleted in the asterisk realtime db.
  //So here, we're marking the assignment as deleted.
  private function ProcessDeleteRequest()
  {
    $assignment = new PhoneNumberAssignment();
    $assignment->LoadByPhoneAndNumber($this->phone_id, $this->number_id);

    //Invert the todelete_assignment bit. This way, people can delete and undelete before the info is sent
    //to the asterisk realtime db.
    $assignment->todelete_assignment = $assignment->todelete_assignment == 1 ? 0 : 1;
    $assignment->SaveToDB();

    //Set altered flag on the phone.
    $this->SetPhoneAltered();

    //Data to send back through the request, so the page knows what happened and to what.
    //Mostly for AJAX style requests.
    $this->todelete_assignment = $assignment->todelete_assignment;
    $this->phone_number_assignment_id = $assignment->phone_number_assignment_id;
  }

  //Helper functions to avoid code duplication.

  private function SaveNumberData($numberObj)
  {
    $phone = Phone::LoadPhoneByID($this->phone_id);
    if ($this->password_index == 1) {
      $numberObj->sip_user = $phone->sip_username1;
      $numberObj->sip_pass = $phone->sip_password1;
    } else {
      $numberObj->sip_user = $phone->sip_username2;
      $numberObj->sip_pass = $phone->sip_password2;
    }

    echo "SaveNumberData<br>";
    $numberObj->number = $this->number;
    $numberObj->callerid = $this->callerid;
    $numberObj->directory_id = $this->directory_id;
    if ($this->IsCreateRequest())
      $numberObj->number_type_id = $this->number_type_id;
    $numberObj->password_index = $this->password_index;
    $numberObj->SaveToDB();
  }

  private function SaveAssignmentData($assignment)
  {
    echo "SaveAssignmentData<br>";
    $assignment->phone_id = $this->phone_id;
    $assignment->number_id = $this->number_id;
    $assignment->number_type_id = $this->number_type_id;
    $assignment->SaveToDB();
    echo "SaveAssignmentData Complete<br>";
  }

  private function SetPhoneAltered()
  {
    echo "SetPhoneAltered()<br>";
    $phone = Phone::LoadPhoneByID($this->phone_id);
    $phone->altered = 1;
    $phone->SaveToDB();
    echo "SetPhoneAltered() Complete<br>";
  }

  private function SetNumberAltered()
  {
    $number = new Number();
    $number->LoadFromDB($this->number_id);
    $number->altered_number = 1;
    $number->SaveToDB();
  }

  private function SetNumberAdded()
  {
    $number = new Number();
    $number->LoadFromDB($this->number_id);
    $number->added_number = 1;
    $number->SaveToDB();
  }


  //-----------------------------
  //



}



?>