<?php

class ProcessorException extends Exception
{

  public const INVALID_ACTION = 1;
  public const INVALID_PHONETYPE = 2;

  public function ThrowInvalidAction()
  {
    $this->code == INVALID_ACTION;
    $this->message = "Invalid action -- Phone is not set to add/edit/delete.";

    throw $this;
  }

  public function ThrowInvalidPhoneType()
  {
    $this->code = INVALID_PHONETYPE;
    $this->message = "Invalid Phone Type -- Not SCCP or SIP.";
  }

  public function MissingSipUserAndPass()
  {
    $this->code = 1000;
    $this->message = "Missing SIP Username and/or Password.";
  }
}

?>