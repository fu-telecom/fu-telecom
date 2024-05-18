<?php
//TODO: Voicemail!!!

class PhoneProcessor extends Controller
{
  private $sccpProcessor;
  private $sipProcessor;

  private $resultProcessor = null;
  private $overallResult = null;

  //To determine action to take.
  public const EDIT_PHONE = 1;
  public const ADD_PHONE = 2;
  public const DELETE_PHONE = 4;

  public function __construct(&$resultProcessor)
  {
    $this->resultProcessor = $resultProcessor;

    $this->sccpProcessor = new SccpProcessor($this->resultProcessor);
    $this->sipProcessor = new SipProcessor($this->resultProcessor);
  }

  private function Log($text)
  {
    $this->resultProcessor->LogText($text);
  }

  public function ProcessAllPhones()
  {
    $this->overallResult = new Result("PhoneProcessorResult");

    $this->Log("<h2>Processing All Phones to Asterisk</h2><br>");

    $modifiedPhoneList = new PhoneList();
    $modifiedPhoneList->LoadModifiedPhones();

    $this->Log("Modifying " . $modifiedPhoneList->GetCount() . " phones.<br><br>");
    $this->overallResult->totalModified = $modifiedPhoneList->GetCount();
    $this->overallResult->errorCount = 0;
    $this->overallResult->addedCount = 0;
    $this->overallResult->editedCount = 0;
    $this->overallResult->deletedCount = 0;
    //Edit/Delete/Add Phones from asteriskrealtime db.
    $this->ProcessPhoneList($modifiedPhoneList);

    //Cleanup
    //Removed deleted lines from system.
    $this->CleanupDeletedItems();
    //Remove numbers that no longer have assignments.
    //TODO: This is a bit of a messy way to do this.
    $this->RemoveUnassignedNumbers();
    //Remove any lines without corresponding button assignments
    $this->sccpProcessor->RemoveUnusedLines();
  }

  //-----------------------------------------
  // Processing functions
  //-----------------------------------------

  private function ProcessPhoneList($modifiedPhoneList)
  {
    $this->Log("<b>Processing the phone list.</b><br>");
    foreach ($modifiedPhoneList->GetList() as $phone) {
      $this->Log("ProcessPhoneList: " . $phone->phone_serial . "<br>");

      $result = new Result("Phone");
      $result->phone_id = $phone->phone_id;
      $result->phone_serial = $phone->phone_serial;

      try {

        $this->ProcessPhone($phone, $result);

      } catch (Exception $e) {
        $result->Log("<h3>An Error Occurred.</h3><br>");
        $result->AnErrorOccured();
        $result->AddErrorMessage($e);

        try {

          $phone->errored = 1;
          $phone->SaveToDB();

          $result->Log("Unwinding any database adds. (For SCCP: Doesn't do sccpline.)<br>");
          $this->UnwindProcess($phone, $result);
        } catch (Exception $e) {
          //Apparently something is very wrong with this phone.
          $result->Log("<h2>Couldn't handle the exception!!!!</h2><br>");
        } finally {
          $this->overallResult->AnErrorOccured();
          $this->overallResult->errorCount += 1;
        }

      } finally {
        $this->resultProcessor->AddResult($result);
      }

    }

    $this->resultProcessor->AddResult($this->overallResult);
  }

  //This will delete from the asterisk realtime db.
  private function ProcessPhone($phone, &$result)
  {
    //Logging for error reporting / result reporting.
    $result->Log("ProcessPhone(): " . $phone->phone_serial . "<br>");
    $result->phone_id = $phone->phone_id;

    $actionToTake = $this->GetActionToTake($phone, $result);

    if ($phone->phone_type_id == PhoneType::SCCP) {
      $result->Log("<h2>Processing SCCP Phone: " . $phone->phone_serial . "</h2>");
      $result->phone_type_name = "SCCP";

      $this->sccpProcessor->ProcessPhone($phone, $actionToTake, $result);
    } else if ($phone->phone_type_id == PhoneType::SIP) {
      $result->Log("<h2>Processing SIP Phone: " . $phone->phone_serial . "</h2>");
      $result->phone_type_name = "SIP";

      $this->sipProcessor->ProcessPhone($phone, $actionToTake, $result);
    } else {
      $error = new ProcessorException();
      $error->ThrowInvalidPhoneType();
    }
  }

  //For errors, unwind db adds.
  private function UnwindProcess($phone, &$result)
  {
    if ($phone->phone_type_id == PhoneType::SCCP) {
      $result->Log("Unwind deleting SCCP Phone<br>");
      $this->sccpProcessor->DeletePhoneAsterisk($phone);
    } else if ($phone->phone_type_id == PhoneType::SIP) {
      $result->Log("Unwind deleting SIP Phone<br>");

      $this->sipProcessor->DeletePhone($phone);

      //Running this remove by name in case the error prevented logging of
      //the sippeer_id field.
      $this->sipProcessor->RemoveFromSippeersByName($phone);
    }
  }

  private function GetActionToTake($phone, &$result)
  {
    $action = 0;

    if ($phone->todelete_phone == 1) {
      $action = self::DELETE_PHONE;

      $result->Log("Phone requires action: DELETE<br>");
      $result->action = "delete";
      $this->overallResult->deletedCount += 1;
    } else if ($phone->added == 1) {
      $action = self::ADD_PHONE;

      $result->Log("Phone requires action: ADD<br>");
      $result->action = "add";
      $this->overallResult->addedCount += 1;
    } else if ($phone->altered == 1 and $phone->todelete_phone == 0 and $phone->added == 0) {
      $action = self::EDIT_PHONE;

      $result->Log("Phone requires action: EDIT<br>");
      $result->action = "edit";
      $this->overallResult->editedCount += 1;
    } else {
      $result->Log("<b>Invalid Action Exception Tripped</b><br>");
      $result->LogVariable("Phone", $phone);
      $error = new ProcessorException();
      $error->ThrowInvalidAction();
    }

    return $action;
  }



  //-------------------------------------
  // Cleanup functions
  //-------------------------------------

  private function CleanupDeletedItems()
  {
    $this->Log("<br>Cleaning up deleted items.<br>");
    $this->CleanupAssignments();
    $this->CleanupPhones();
  }

  private function CleanupAssignments()
  {
    $this->Log("Cleaning up deleted assignments.<br>");
    $deletionList = new PhoneNumberAssignmentList();
    $deletionList->LoadMarkedForDeletion();

    $this->overallResult->deletedAssignments = $deletionList->GetCount();

    foreach ($deletionList->GetList() as $assignment) {
      $this->Log("Deleting (phone/number): " . $assignment->phone_id .
        "/" . $assignment->number_id);

      $assignment->DeleteFromDB();
    }
  }

  //Warning: This will delete phones without checking phone_is_deployed
  private function CleanupPhones()
  {
    $this->Log("Cleaning up deleted phones.<br>");

    $phoneList = new PhoneList();
    $phoneList->LoadMarkedForDeletion();

    $this->overallResult->deletedPhones = $phoneList->GetCount();

    foreach ($phoneList->GetList() as $phone) {
      $this->Log("Deleting: " . $phone->phone_serial . "<br>");
      $phone->DeleteFromDB();

    }
  }

  //Removes any numbers that are no longer assigned.
  private function RemoveUnassignedNumbers()
  {
    $this->Log("Removing unassigned numbers.");

    $unassignedList = new NumberList();
    $unassignedList->LoadUnassignedNumbers();

    $this->overallResult->unassignedCount = $unassignedList->GetCount();

    foreach ($unassignedList->GetList() as $number) {
      $this->Log("Deleting (number): " . $number->number . "<br>");
      $number->DeleteFromDB();
      $this->sccpProcessor->RemoveExtensions($number->number);
    }
  }




  //----------------------------------------
  // Extra functions, possibly for cleanup purposes,
  //  probably not going to be used, etc.
  //----------------------------------------

  public function ResetAllAsteriskData()
  {

    //Start from clean slate in SCCP related tables.
    $sccplineqry = "DELETE FROM asteriskrealtime.sccpline";
    $sccpdeviceqry = "DELETE FROM asteriskrealtime.sccpdevice";
    $buttonconfigqry = "DELETE FROM asteriskrealtime.buttonconfig";
    $extensionqry = "DELETE FROM asteriskrealtime.extensions";
    $voicemailqry = "DELETE FROM asteriskrealtime.voicemail";
    $sippeersqry = "DELETE FROM asteriskrealtime.sippeers";

    $resetQuery = "UPDATE fuconfig.phones SET added = 1 WHERE todelete_phone = 0";

    FUConfig::ExecuteQuery($sccplineqry);
    FUConfig::ExecuteQuery($sccpdeviceqry);
    FUConfig::ExecuteQuery($buttonconfigqry);
    FUConfig::ExecuteQuery($extensionqry);
    FUConfig::ExecuteQuery($voicemailqry);
    FUConfig::ExecuteQuery($sippeersqry);

    FUConfig::ExecuteQuery($resetQuery);
  }












}






?>