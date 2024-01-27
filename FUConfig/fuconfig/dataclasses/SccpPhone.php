<?php

class SccpPhone extends Phone {
	public function CreateEmpty() {
		Parent::CreateEmpty();

		$this->phone_type_id = PhoneType::SCCP;
	}

	
}


?>
