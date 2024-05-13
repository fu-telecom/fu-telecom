<?php

include_once ('FUConfig.php');

$routerHandler = new RouterHandler();

$router = new Router();
$router->LoadFromDB(1);

//$routerHandler->CheckAndUpdateRouter($router);
//OutputXML($routerHandler->AsXML());


$connection = ssh2_connect('172.16.50.1');
ssh2_auth_password($connection, 'root', 'n23n23129');
$dlinkInstallFile = "/tftproot/openwrt/dlink_install_5-2019.sh";
$destinationFile = "/root/dlink_install_5-2019.sh";

ssh2_scp_recv($connection, $destinationFile, $dlinkInstallFile);

//$result = ssh2_connect('localhost');
//var_dump($result);
/*
$connection = ssh2_connect('172.16.50.1');
ssh2_auth_password($connection, 'root', 'n23n23129');

$dlinkInstallFile = "/tftproot/openwrt/dlink_install_script_5-2019.sh";
$destinationFile = "/root/test.sh";

ssh2_scp_send($connection, $dlinkInstallFile, $destinationFile, 0774);
*/
/*
$stdout = ssh2_exec($connection, 'cat /etc/config/asdfs');
$stderr = ssh2_fetch_stream($stdout, SSH2_STREAM_STDERR);
stream_set_blocking($stdout, true);
stream_set_blocking($stderr, true);

echo "<b>stdout:</b> <br>";
while ($line = fgets($stdout)) {
  flush();
  echo $line . "<br/>";
}

echo "<b>stderr:</b> <br>";
while ($line = fgets($stderr)) {
  flush();
  echo $line . "<br/>";
}

*/


/*$err_buf = "";
$out_buf = "";

do {
  $err_buf.= fread($stderr, 4096);
  $out_buf.= fread($stdout, 4096);

  // Wait here so we don't hammer in this loop
  sleep(1);

} while (feof($stderr) == false and feof($stdout) == false);
*/



//echo "STDERR:\n$err_buf\n";
//echo "STDOUT:\n$out_buf\n";

//echo "Done\n";

?>