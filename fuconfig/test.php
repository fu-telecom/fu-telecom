<?php

include_once ('FUConfig.php');

$routerHandler = new RouterHandler();

$router = new Router();
$router->LoadFromDB(1);

$connection = ssh2_connect('172.16.50.1');
ssh2_auth_password($connection, 'root', 'n23n23129');
$dlinkInstallFile = "/tftproot/openwrt/dlink_install_5-2019.sh";
$destinationFile = "/root/dlink_install_5-2019.sh";

ssh2_scp_recv($connection, $destinationFile, $dlinkInstallFile);

?>