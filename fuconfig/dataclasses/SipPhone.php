<?php

class SipPhone extends Phone
{

  public function CreateEmpty()
  {
    parent::CreateEmpty();

    $this->phone_type_id = PhoneType::SIP;
  }



}


?>