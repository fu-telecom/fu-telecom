<?php

//Asterisk Realtime Controller
class SccpProcessor
{
  private $processResult = null;

  private $result = null; //Current working result.

  private $linesAdded = array();

  public function __construct($processResult = null, $result = null)
  {
    //TODO: Make the process result sections optional somehow.
    if ($processResult == null) {
      $this->processResult = new ProcessResult();
      $this->result = new Result("Phone");
    } else
      $this->processResult = $processResult;

  }

  public function ProcessPhone($phone, $actionToTake, &$result)
  {
    $this->result = $result;
    $this->linesAdded = array(); //Wipe array.

    if ($actionToTake == PhoneProcessor::DELETE_PHONE) {
      $this->result->Log("<b>Deleting Phone</b><br>");
      $this->DeletePhoneAsterisk($phone);
    }

    if ($actionToTake == PhoneProcessor::EDIT_PHONE) {
      $this->result->Log("<b>Editing Phone</b><br>");
      $this->DeletePhoneAsterisk($phone);
      $this->AddPhoneAsterisk($phone);
      $this->ReloadPhone($phone);
    }

    if ($actionToTake == PhoneProcessor::ADD_PHONE) {
      $this->result->Log("<b>Adding Phone</b><br>");
      $this->AddPhoneAsterisk($phone);
      $this->ReloadPhone($phone);
    }

  }

  public function RemoveUnusedLines()
  {
    $unusedLineList = new SccpLineList();
    $unusedLineList->LoadUnassignedLines();

    foreach ($unusedLineList->GetList() as $line) {
      $line->DeleteFromDB();
    }
  }

  public function ReloadPhone($phone)
  {
    $this->result->Log("Reloading SCCP Phone In Asterisk");
    $reloadcmd = 'sudo asterisk -x "sccp restart ' .
      $phone->phone_serial . '"';

    $this->result->Log($reloadcmd . "<br>");

    $resultText = shell_exec($reloadcmd);

    return $resultText;
  }







  //----------------------------------
  // Delete Functionality
  //----------------------------------

  public function DeletePhoneAsterisk($phone)
  {
    $this->result->Log("<br>DeletePhoneAsterisk() called. Phone: " . $phone->phone_id . "<br>");
    $this->RemoveDevice($phone);
    $this->RemoveButtons($phone);
  }

  private function DeleteLineAsterisk($number)
  {
    $sccpline = new SccpLine();
    $sccpline->LoadFromDB($number->sccpline_id);
    $sccpline->DeleteFromDB();
  }

  private function RemoveDevice($phone)
  {
    $this->result->Log("<br>RemoveDevice() called. Phone: " . $phone->phone_id . "<br>");
    $sccpDevice = new SccpDevice();

    if ($sccpDevice->LoadByDeviceName($phone->phone_serial)) {
      $sccpDevice->DeleteFromDB();
    }

    $phone->SaveToDB();
  }

  private function RemoveButtons($phone)
  {
    $this->result->Log("RemoveButtons()" . $phone->phone_serial . "<br>");

    //Call delete all.
    $buttonConfigList = new ButtonConfigList();
    $buttonConfigList->DeleteByDeviceName($phone->phone_serial);
  }

  private function RemoveLine($sccpline)
  {
    $this->result->Log("RemoveLine() for " . $sccpline->id . "<br>");

    $sccpline->DeleteFromDB();

    $this->RemoveSccplineExtensions($sccpline);
    $this->RemoveVoicemail($sccpline);
  }

  private function RemoveSccplineExtensions($sccpline)
  {
    $this->RemoveExtension($sccpline->id);
  }
  //I could probably do this with cascade delete,
  //but I don't feel like testing.
  public function RemoveExtensions($number)
  {
    $this->result->Log("RemoveExtensions() for " . $number . "<br>");

    $extensionList = new ExtensionList();
    $extensionList->LoadByExten($number);

    foreach ($extensionList->GetList() as $extension) {
      $extension->DeleteFromDB();
    }
  }

  private function RemoveVoicemail($sccpline)
  {
    $this->result->Log("RemoveVoicemail() for " . $sccpline->id . "<br>");

    $vm = new Voicemail();
    $vm = $vm->LoadByNumber($sccpline->id);

    if ($vm == null) {
      $this->result->Log("Voicemail Not Found.<br>");
      return null;
    }

    $this->result->Log("Voicemail removed.<br>");
    $vm->DeleteFromDB();
  }





  //----------------------------------
  // Add Functionality
  //----------------------------------

  //Add phone, line, and buttons.
  public function AddPhoneAsterisk($phone)
  {
    $this->result->Log("AddPhoneAsterisk() called.<br><br>");
    //Used for line and buttons.
    $assignmentList = new PhoneNumberAssignmentList();
    $assignmentList->LoadByPhoneId($phone->phone_id);

    $this->result->Log("AddDevice called for:" . $phone->phone_serial . "<br>");
    $this->AddDevice($phone);

    $count = 0;

    foreach ($assignmentList->GetList() as $assignment) {

      $number = $assignment->GetNumber();

      //This one is getting removed. Skip it.
      if ($number->todelete_number == 1 or $assignment->todelete_assignment == 1) {

        continue;
      }


      //Lines go in sccpline -- once.
      if ($assignment->GetNumberType()->number_type_id == NumberType::LINE) {
        $this->AddLine($phone, $number);
      }

      $this->result->Log("AddButton()<br>");
      $this->AddButton($phone, $assignment, $number, $assignment->display_order);
      $count++;
    }

    //If this model has side carriages, add buttons to them.
    $this->AddButtonList($phone, $count);

    //Last step is to clear any phone or line flags and mark deployed.
    //This way, if there are any errors, it won't mess things up.
    $this->ClearPhoneFlags($phone);
    $this->ClearNumberFlagsForLinesAdded();
  }

  //TODO: See if this can go in PhoneProcessor. It's duped in SipProcessor
  private function ClearPhoneFlags($phone)
  {
    $phone->altered = 0;
    $phone->added = 0;
    $phone->errored = 0;
    $phone->SaveToDB();
  }

  //TODO: See if this can go in PhoneProcessor.
  private function ClearNumberFlagsForLinesAdded()
  {
    foreach ($this->linesAdded as $number) {
      $number->added_number = 0;
      $number->altered_number = 0;
      $number->SaveToDB();
    }

  }

  //Adds to sccpdevice table and returns SccpDevice
  private function AddDevice($phone): SccpDevice
  {
    $sccpDevice = new SccpDevice();
    $sccpDevice->type = $phone->GetPhoneModel()->phone_model_system_name;
    $sccpDevice->description = $phone->GetOrg()->org_name;
    $sccpDevice->name = $phone->phone_serial;
    $sccpDevice->SaveToDB();

    $phone->sccpdevice_id = $sccpDevice->id;
    $phone->SaveToDB();

    $this->AddDeviceXmlConfig($phone);

    return $sccpDevice;
  }

  private function AddDeviceXmlConfig($phone)
  {
    $this->result->Log("Copying XML Config - ");
    $defaultXmlFilename = $phone->GetPhoneModel()->xml_config_filename;

    $sepxmlcmd = 'cp /tftproot/' . $defaultXmlFilename .
      " /tftproot/" . $phone->phone_serial . '.cnf.xml >&1; echo $?';

    //Execute command and log result.
    $resultNumber = trim(shell_exec($sepxmlcmd));
    $resultText = $resultNumber == 0 ? "Success" : "Failure";

    //Add as an element.
    $this->result->xmlConfig = $resultNumber;

    $this->result->Log("Result (" . $resultNumber . "): " . $resultText . "<br>");
  }

  private function AddLine($phone, $number)
  {
    $include_id_in_insert = true;

    $this->result->Log("<br>Add Line: " . $number->number . " " . $number->callerid . "<br>");

    //Check if the line is already in the db. Null if not.
    $sccpline = $this->LineAlreadyExists($number);

    //If it exists and it's modified, remove it to readd.
    //Else skip this one, it's already in the db.
    if ($sccpline != null) {
      if ($number->number == $sccpline->id) {
        $this->result->Log("Line already exists and matches, so no need to add.<br>");
        return null;
      } else {
        $this->result->Log("Line is modified. Removing existing line to update it.<br>");
        //Removes line and extensions.
        $this->RemoveLine($sccpline);

      }
    }

    $this->result->Log("Adding sccpline to DB.<br>");

    $sccpline = new SccpLine(); //Create from empty.
    $sccpline->id = $number->number;
    $sccpline->pin = $number->number;
    $sccpline->label = $number->number . "-" . $number->callerid;
    $sccpline->description = $number->callerid;
    $sccpline->mailbox = $number->number;
    $sccpline->cid_name = $number->callerid;
    $sccpline->cid_num = $number->number;
    $sccpline->dnd = 'off';
    $sccpline->name = $number->number;
    $sccpline->SaveToDB($include_id_in_insert);

    $this->result->Log("Saved SccpLine - ID: " . $sccpline->id . "<br>");

    $this->result->Log("Updating Number and clearing flags.<br>");

    $number->sccpline_id = $sccpline->id;
    $number->SaveToDB();

    $this->linesAdded[] = $number;

    $this->AddToExtensions($number, $sccpline);
    $this->AddToVoicemail($phone, $number);

  }

  // TODO: Put this all in the PhoneProcessor.
  private function AddToExtensions($number, $sccpline)
  {
    $dialExtension = new Extension();
    $vmExtension = new Extension();

    $dialExtension->context = "default";
    $dialExtension->exten = $number->number;
    $dialExtension->priority = 1;
    $dialExtension->app = "Dial";
    $dialExtension->appdata = "SCCP/" . $number->number;
    $dialExtension->sccpline_id = $sccpline->id;
    $dialExtension->SaveToDB();

    $vmExtension->context = "default";
    $vmExtension->exten = $number->number;
    $vmExtension->priority = 2;
    $vmExtension->app = "Voicemail";
    $vmExtension->appdata = $number->number . "@default,u";
    $vmExtension->sccpline_id = $sccpline->id;
    $vmExtension->SaveToDB();
  }

  // TODO: Put this all in the PhoneProcessor.
  private function AddToVoicemail($phone, $number)
  {
    $this->result->Log("AddToVoicemail() for " . $number->number . "<br>");
    $vm = new Voicemail();
    $vm->mailbox = $number->number;
    $vm->fullname = $phone->GetOrg()->org_name;
    $vm->SaveToDB();
  }

  private function AddButton($phone, $assignment, $number, $instance)
  {
    $this->result->Log("Adding Button: " . $number->number . "<br>");
    $button = new ButtonConfig();
    $button->device = $phone->phone_serial;
    $button->instance = $instance;

    $button->sccpdevice_id = $phone->sccpdevice_id;
    $button->type = $assignment->GetNumberType()->number_type_system_name;

    if ($assignment->GetNumberType()->number_type_id == NumberType::SPEEDDIAL) {
      $button->name = $number->callerid;
      $button->options = $number->number . "," . $number->number . "@hints";
    } else if ($assignment->GetNumberType()->number_type_id == NumberType::LINE) {
      $button->name = $number->number;// $number->callerid;
      $button->options = $number->number;
    } else if ($assignment->GetNumberType()->number_type_id == NumberType::RANDOM) {
      $button->name = $number->callerid;
      $button->options = $number->number; // . "@default";
    } else {
      //No clue at this point. Custom types can go in here.
    }

    $button->SaveToDB();
  }






  //---------------------------------------------------------
  // Button List Generation
  //---------------------------------------------------------

  private function AddEmptyButtons($phone, $max, $startIndex = 1)
  {
    $index = $startIndex;
    $this->result->Log("AddEmptyButtons(Phone, $max, " . $index . ")<br>");

    while ($index <= $max) {
      $buttonConfig = $this->MakeEmptyButton($phone, $index);

      $index++;
    }
  }

  private function MakeEmptyButton($phone, $instance)
  {
    $buttonConfig = new ButtonConfig();
    $buttonConfig->device = $phone->phone_serial;
    $buttonConfig->instance = $instance;
    $buttonConfig->type = "empty";
    $buttonConfig->name = "empty";
    $buttonConfig->options = "empty";
    $buttonConfig->SaveToDB();

    return $buttonConfig;
  }

  private function FillEmpty($phone, $count)
  {
    $this->result->Log("Fill Unused Number Slots on Phone.<br>");
    $maxNumbers = $phone->GetPhoneModel()->phone_model_max_numbers;

    if ($count < $maxNumbers)
      $this->AddEmptyButtons($phone, $maxNumbers, $count + 1);

  }

  private function AddAllLinesAsButtons($phone)
  {
    $buttonMax = $phone->GetPhoneModel()->button_list_max;
    $numberList = new NumberList();
    $numberList->LoadAllDisplayableLines();

    $index = 0;
    $buttonListMax = $phone->GetPhoneModel()->button_list_max;
    $pageSize = $phone->GetPhoneModel()->page_size;
    $pageCount = $buttonListMax / $phone->GetPhoneModel()->page_size;
    $buttonArray = $this->FillEmptyNumberArray($buttonListMax);

    foreach ($numberList->GetList() as $number) {
      $page = floor($index / $pageSize);
      $targetPos = 0;

      if ($pageCount == 4) {
        if ($page == 0) {
          //0 - 11
          $targetPos = $index;
        } else if ($page == 1) {
          //24 - 35
          $targetPos = $index + $pageSize;
        } else if ($page == 2) {
          //12 - 23
          $targetPos = $index - $pageSize;
        } else {
          //36 - 48
          $targetPos = $index;
        }

      } else if ($pageCount == 2) {
        $targetPos = $index;
      }

      $this->result->Log("Adding number to page $page for button array at $targetPos<br>");
      $buttonArray[$targetPos] = $number;

      $index++;
    }

    $this->AddNumberArrayToButtonConfig($phone, $buttonArray);

  }

  private function AddCustomButton($phone, $number, $instance)
  {
    $buttonConfig = new ButtonConfig();
    $buttonConfig->instance = $instance;
    $buttonConfig->device = $phone->phone_serial;
    $buttonConfig->type = "speeddial";
    $buttonConfig->name = $number->callerid;
    $buttonConfig->options = $number->number;
    $buttonConfig->SaveToDB();
  }

  private function AddNumberArrayToButtonConfig($phone, $array)
  {
    //
    $index = 0;
    $startInstance = 1 + $phone->GetPhoneModel()->phone_model_max_numbers;

    while ($index < count($array)) {
      $targetPos = $index + $startInstance;

      if ($array[$index] != null) {
        $this->AddCustomButton($phone, $array[$index], $targetPos);
      } else {
        $this->MakeEmptyButton($phone, $targetPos);
      }

      $index++;
    }

  }


  private function FillEmptyNumberArray($size)
  {
    $arr = array();
    $i = 0;

    while ($i < $size) {
      $arr[] = null;
      $i++;
    }

    return $arr;
  }

  private function AddButtonList($phone, $count)
  {
    $this->result->Log("<h2>Add Buttons List - $count</h2>");

    if ($phone->GetPhoneModel()->add_button_list == 0) {
      $this->result->Log("Phone model has no side carriages.<br>");
      return;
    }


    //Fill out unused number locations with empty.
    //This is a precondition of putting numbers in the side carriages.
    $this->result->Log("FillEmpty()<br>");
    $this->FillEmpty($phone, $count);

    $this->AddAllLinesAsButtons($phone);
  }

  //-------------------------------------------
  // Support functions
  //-------------------------------------------

  //If line already exists, return it. Otherwise, return null.
  private function LineAlreadyExists($number)
  {
    $this->result->Log("Check if LineAlreadyExists: " . $number->sccpline_id . " - ");
    if ($number->sccpline_id == null or $number->sccpline_id == "") {
      $this->result->Log("It does NOT exist.");
      return null;
    }

    $sccpline = new SccpLine();
    $sccpline = $sccpline->LoadByNumber($number->sccpline_id);

    if ($sccpline == null) {
      $this->result->Log("It does NOT exist.<br>");
    } else {
      $this->result->Log("It DOES exist.<br>");
    }

    return $sccpline;
  }

  private function LineIsModified($number, $sccpline): bool
  {
    if ($number->number != $sccpline->id)
      return false;

    if ($number->callerid != $sccpline->description)
      return false;

    return true;
  }




}






?>