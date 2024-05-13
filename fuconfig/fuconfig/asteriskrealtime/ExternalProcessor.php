<?php

class ExternalProcessor
{
  private static $numberAdditionList = array();
  private static $numberDeletionList = array();

  public function ProcessExternalFiles(&$processResult)
  {
    $extensionsProcessor = new ExtensionsProcessor();
    $extensionProcessor->ProcessExtensions(self::$numberAdditionList, self::$numberDeleteList);


  }

  public function WipeLists()
  {
    self::$numberAdditionList = array();
    self::$numberDeleteList = array();
  }
}


?>