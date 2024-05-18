<?php

class RouterHandler extends Controller
{
  private const CURRENT_VERSION = 10;
  private const REMOTE_SCRIPT = "/root/dlink_install_web.sh";
  private const LOCAL_SCRIPT = "/tftproot/openwrt/dlink_install_5-2019.sh";

  private const ROUTER_USER = "root";
  private const ROUTER_PASS = "n23n23129";

  private $ssh = null;
  private $router = null;

  public function __construct()
  {
    $this->ssh = new SSH();
  }

  public function CheckAndUpdateRouter($router)
  {
    $this->router = $router;

    //Set flags for return data to defaults.
    $this->router_id = $router->router_id;
    $this->error = 0;
    $this->message = "";
    $this->script_update = 0;
    $this->channel_update = 0;
    $this->version = 0;
    $this->current_24 = 0;
    $this->current_5 = 0;
    $this->complete = 0;

    try {
      //Make connection.
      $connected = $this->ssh->Connect(
        $this->router->GetIP(),
        self::ROUTER_USER,
        self::ROUTER_PASS
      );

      if (!$connected) {
        $this->error = 1;
        $this->message = "Unable to make connection.";

        return false;
      }

      //Check the update script and the channels.
      $this->Update();

      //Mark as complete.
      $this->complete = 1;

    } catch (Exception $e) {
      $this->error = 1;
      $this->message = $e->getMessage();
    }
  }

  private function Update()
  {

    if ($this->ScriptUpdateRequired()) {
      $this->script_update = 1;

      $this->SendDlinkInstallFile();
      $this->SetVersion(self::CURRENT_VERSION);
      $this->ExecuteInstallFile();
    }

    if ($this->ChannelUpdateRequired()) {
      $this->channel_update = 1;
      $this->ExecuteInstallFile();
    }
  }

  private function ChannelUpdateRequired(): bool
  {
    $this->GetCurrentChannelSettings();

    if ($this->router->channel_24 != $this->router->channel_24_current)
      return true;

    if ($this->router->channel_5 != $this->router->channel_5_current)
      return true;

    return false;
  }

  private function GetCurrentChannelSettings()
  {
    $this->ssh->Execute('uci show wireless.radio0.channel');
    $uci24 = $this->ssh->GetOutput();

    $this->router->channel_24_current = $this->GetChannelFromUci($uci24);

    $this->ssh->Execute('uci show wireless.radio1.channel');
    $uci5 = $this->ssh->GetOutput();

    $this->router->channel_5_current = $this->GetChannelFromUci($uci5);

    $this->current_24 = $this->router->channel_24_current;
    $this->current_5 = $this->router->channel_5_current;

    $this->router->SaveToDB();
  }

  private function GetChannelFromUci($uci)
  {
    $result = explode("=", $uci);
    return (int) $result[1];
  }

  //-----------------------------------------
  // Script / Version functions
  //-----------------------------------------

  private function ExecuteInstallFile()
  {
    $cmd = self::REMOTE_SCRIPT . " " . $this->router->number . " 1 0 " .
      $this->router->channel_24 . " " . $this->router->channel_5;

    $this->execute = $cmd;

    $this->ssh->Execute($cmd);
    $this->ssh->GetLongOutput(); //This includes a timeout feature.
  }

  private function ScriptUpdateRequired(): bool
  {
    $version = $this->GetVersion();

    if (strlen($version) == 0 or $version < self::CURRENT_VERSION)
      return true;

    return false;
  }

  private function SendDlinkInstallFile()
  {
    $createMode = 0775;

    $this->ssh->SendFile(self::LOCAL_SCRIPT, self::REMOTE_SCRIPT, $createMode);
  }

  private function GetVersion()
  {
    $this->ssh->Execute('touch /etc/FUVersion');
    $this->ssh->Execute('cat /etc/FUVersion');

    $version = trim($this->ssh->GetOutput());

    $this->version = $version;

    return $version;
  }

  private function SetVersion($version)
  {
    $this->ssh->Execute('echo ' . $version . " > /etc/FUVersion");
  }
}


?>