<?php

include_once ('FUConfig.php');

$resultProcessor = new ProcessResult(false);
$processor = new PhoneProcessor($resultProcessor);

$processor->ResetAllAsteriskData();

?>