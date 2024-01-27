<?php

class OrgList extends DataList {
	
	
	public $OrgList = array();
	
	public function Setup() {
		
	}
	
	/*public function Orgs() {
		return $this->OrgList;
	}
	
	public function OrgsCount() {
		if ($loaded == 0) 
		{
			$trace = debug_backtrace();
			trigger_error(
				'Read-only property via __set(): ' . $name .
				' in ' . $trace[0]['file'] .
				' on line ' . $trace[0]['line'],
				E_USER_NOTICE);
			return null;
		}
		
		return count($this->OrgList);
	}*/
	
	public function LoadAllOrgs() {
		$orgsListQuery = "SELECT orgs.*
					FROM fuconfig.orgs
					ORDER BY orgs.org_name;";
					
		$this->OrgList = $this->LoadListFromQuery($orgsListQuery, "Org");
	}
	
	public function LoadPhonesForOrgs() {
		foreach ($OrgList as &$org) {
			$org->LoadPhones();
		}
	}
	
	
	
}










?>