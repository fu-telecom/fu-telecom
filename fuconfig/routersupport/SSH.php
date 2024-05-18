<?php

class SSH
{
  private $stdout;
  private $stderr;

  private $output;
  private $error;

  private $connection;

  private $useErrorStream = false;

  private $connectionMade = false;

  public function __construct($useErrorStream = false)
  {
    $this->useErrorStream = $useErrorStream;

    set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) {
      // error was suppressed with the @-operator
      if (0 === error_reporting()) {
        return false;
      }

      throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    });

  }

  public function __destruct()
  {
    if ($this->connectionMade)
      ssh2_exec($this->connection, 'exit');
  }

  public function Connect($hostname, $user, $pass)
  {
    $this->connection = ssh2_connect($hostname);
    ssh2_auth_password($this->connection, $user, $pass);

    if (!$this->connection) {
      $this->connectionMade = false;
      return false;
    }

    $this->connectionMade = true;
    return true;
  }

  public function Execute($command)
  {
    if ($this->connectionMade == false)
      throw new Exception("Use Connect() before trying to send commands.");

    $this->stdout = ssh2_exec($this->connection, $command);

    if ($this->useErrorStream) {
      $this->stderr = ssh2_fetch_stream($this->stdout, SSH2_STREAM_STDERR);
      stream_set_blocking($this->stderr, true);
    }

    stream_set_blocking($this->stdout, true);


  }

  public function GetOutput()
  {
    $output = "";

    $output = stream_get_contents($this->stdout);

    return $output;
  }

  public function GetOutputStream()
  {
    return $this->stdout;
  }

  public function GetErrorOutput()
  {
    $output = stream_get_contents($this->stderr);

    return $output;
  }

  public function SendFile($localFile, $destinationFile, $create_mode = 0644)
  {
    ssh2_scp_send($this->connection, $localFile, $destinationFile, $create_mode);
  }

  public function GetLongOutput()
  {
    $out_buf = "";
    $done = 0;
    $t0 = time();

    do {
      $out_buf .= fread($this->stdout, 4096);

      if (feof($this->stdout))
        $done++;

      $t1 = time();
      $span = $t1 - $t0;

    } while (($span < 12) and ($done == 0));

    return $out_buf;
  }



}

?>