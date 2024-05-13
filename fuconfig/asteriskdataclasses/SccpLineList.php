<?php

class SccpLineList extends DataList
{

  public function Setup()
  {

  }

  public function LoadUnassignedLines()
  {
    $query = "SELECT sccpline.* FROM asteriskrealtime.sccpline
							WHERE sccpline.id IN (
										SELECT sccpline.id FROM asteriskrealtime.sccpline
											LEFT JOIN asteriskrealtime.buttonconfig
												ON sccpline.id LIKE buttonconfig.name
											GROUP BY sccpline.id
											HAVING COUNT(buttonconfig.name) = 0)";

    $this->LoadListFromQuery($query, "SccpLine");
  }

}


?>