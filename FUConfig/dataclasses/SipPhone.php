<?php

class SipPhone extends Phone {

	public function CreateEmpty() {
		Parent::CreateEmpty();

		$this->phone_type_id = PhoneType::SIP;
	}

	

}


?>
