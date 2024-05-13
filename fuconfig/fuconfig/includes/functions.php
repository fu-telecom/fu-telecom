<?php


function Redirect($url, $permanent = false)
{
  if (headers_sent() === false) {
    header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
  }

  exit();
}

function OutputXML($xml)
{
  //Output data produced.
  ob_clean();
  Header('Content-Type: text/xml');
  print ($xml->asXML());
}

function GetXMLItemValue($xml, $xpath)
{
  $xpathResult = $xml->xpath($xpath);

  if (count($xpathResult) < 1) {
    return null;
  } else {
    var_dump($xpathResult);
    $item = $xpathResult[0];

    return $item->nodeValue;
  }
}

?>