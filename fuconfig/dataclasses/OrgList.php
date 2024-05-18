<?php

class OrgList extends DataList
{


  public $OrgList = array();

  public function Setup()
  {

  }

  public function LoadAllOrgs()
  {
    $orgsListQuery = "SELECT orgs.*
					FROM fuconfig.orgs
					ORDER BY orgs.org_name;";

    $this->OrgList = $this->LoadListFromQuery($orgsListQuery, "Org");
  }

  public function LoadPhonesForOrgs()
  {
    foreach ($OrgList as &$org) {
      $org->LoadPhones();
    }
  }



}










?>