<?php

class SipProcessor
{
  private $processResult = null;

  private $result = null; //Current working result.

  private $linesAdded = array();

  public function __construct($processResult)
  {
    $this->processResult = $processResult;
  }

  public function ProcessPhone($phone, $actionToTake, &$result)
  {
    $this->result = $result;
    $this->linesAdded = array(); //Wipe array.

    if ($actionToTake == PhoneProcessor::DELETE_PHONE) {
      $this->result->Log("<b>Deleting Phone</b><br>");
      $this->DeletePhone($phone);
    }

    if ($actionToTake == PhoneProcessor::EDIT_PHONE) {
      $this->result->Log("<b>Editing Phone</b><br>");
      $this->DeletePhone($phone);
      $this->AddPhone($phone);
    }

    if ($actionToTake == PhoneProcessor::ADD_PHONE) {
      $this->result->Log("<b>Adding Phone</b><br>");
      $this->AddPhone($phone);
    }

    $this->ClearNumberFlagsForLinesAdded();

    return $this->result;
  }

  private function AddPhone($phone)
  {
    $this->result->Log("Adding Phone: " . $phone->phone_serial . "<br>");

    $assignmentList = new PhoneNumberAssignmentList();
    $assignmentList->LoadByPhoneId($phone->phone_id);

    foreach ($assignmentList->GetList() as $assignment) {
      if ($assignment->todelete_assignment == 1)
        continue; //getting deleted, skip it.

      $number = $assignment->GetNumber();
      $this->AddToAsterisk($phone, $number);
    }

    $this->result->Log("Clearing Phone Flags: " . $phone->phone_serial . "<br>");
    $this->ClearPhoneFlags($phone);
  }

  private function AddToAsterisk($phone, $number)
  {
    $this->result->Log("Adding number to asterisk: " . $number->number .
      " - " . $number->callerid . "<br>");

    if ($number->todelete_number == 1) {
      $this->result->Log("Skipping this number -- it's marked for deletion.<br>");
      return null;
    }

    if ($number->sip_user == null) {
      $this->result->Log("Username or password not set! Can't add.<br>");
      $exception = new ProcessorException();
      $exception->MissingSipUserAndPass();
      throw $exception;
      return null;
    }

    $sipPeer = new SipPeer();
    $sipPeer->name = $number->sip_user;
    $sipPeer->secret = $number->sip_pass;
    $sipPeer->callerid = $number->callerid;
    $sipPeer->cid_number = $number->callerid;
    $sipPEer->type = "peer";
    $sipPeer->SaveToDB();

    $number->sippeer_id = $sipPeer->id;
    $number->SaveToDB();

    $this->linesAdded[] = $number;

    $this->AddToExtensions($number);
    $this->AddToVoicemail($phone, $number);
  }

  public function DeletePhone($phone)
  {
    $this->result->Log("Deleting Phone: " . $phone->phone_serial . "<br>");

    $assignmentList = new PhoneNumberAssignmentList();
    $assignmentList->LoadByPhoneId($phone->phone_id);

    foreach ($assignmentList->GetList() as $assignment) {
      $number = $assignment->GetNumber();
      $this->DeleteFromAsterisk($number);


    }
  }

  public function RemoveFromSippeersByName($phone)
  {
    $this->result->Log("Removing SIP phone by name." . $phone->phone_serial . "<br>");

    $numberList = new NumberList();
    $numberList->LoadByPhoneAssignment($phone->phone_id);

    foreach ($numberList->GetList() as $number) {
      $sipPeer = new SipPeer();
      $sipPeer = $sipPeer->LoadByName($number->sip_user);

      if ($sipPeer != null) {
        $this->result->Log("Deleting Found Name: " . $number->sip_user . "<br>");
        $sipPeer->DeleteFromDB();
      }
    }
  }

  private function DeleteFromAsterisk($number)
  {
    $this->result->Log("Deleting data from asterisk for number: " . $number->number .
      " - " . $number->callerid . ". Sippeer id: " . $number->sippeer_id . "<br>");

    $sippeer_id = $number->sippeer_id;

    if ($sippeer_id == null) {
      $this->result->Log("Number is missing sippeer_id.<br>");
      return null;
    }

    $sipPeer = new SipPeer();
    $sipPeer->LoadFromDB($sippeer_id);
    $sipPeer->DeleteFromDB();

    $number->sippeer_id = null;
    $number->SaveToDB();
  }

  //-------------------------------------------------
  // Duplicated code -- yeah I know, bad, but I am out of time.
  // NOTE: There is a new $number->GetAppNumber() for the SIP/1234 style.
  // TODO: Put this all in the PhoneProcessor.
  //---------------------------------------------------

  private function AddToExtensions($number)
  {
    $dialExtension = new Extension();
    $vmExtension = new Extension();

    $dialExtension->context = "default";
    $dialExtension->exten = $number->number;
    $dialExtension->priority = 1;
    $dialExtension->app = "Dial";
    $dialExtension->appdata = "SIP/" . $number->sip_user;
    $dialExtension->SaveToDB();

    $vmExtension->context = "default";
    $vmExtension->exten = $number->number;
    $vmExtension->priority = 2;
    $vmExtension->app = "Voicemail";
    $vmExtension->appdata = $number->number . "@default,u";
    $vmExtension->SaveToDB();
  }

  private function AddToVoicemail($phone, $number)
  {
    $this->result->Log("AddToVoicemail() for " . $number->number . "<br>");
    $vm = new Voicemail();
    $vm->mailbox = $number->number;
    $vm->fullname = $phone->GetOrg()->org_name;
    $vm->SaveToDB();
  }

  private function ClearPhoneFlags($phone)
  {
    $phone->altered = 0;
    $phone->added = 0;
    $phone->errored = 0;
    $phone->SaveToDB();
  }

  private function ClearNumberFlagsForLinesAdded()
  {
    foreach ($this->linesAdded as $number) {
      $number->added_number = 0;
      $number->altered_number = 0;
      $number->SaveToDB();
    }

  }
}



?>