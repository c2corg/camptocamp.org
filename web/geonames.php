<?php
// simple script acting as proxy for geonames, since it is not available
// on https

// check if referer is ok
$referer = parse_url($_SERVER['HTTP_REFERER'])['host'];
if ($referer !== $_SERVER['HTTP_HOST']) {
  header('HTTP/1.0 400 Bad Request');
  exit('invalid referer');
}

if (!$_SERVER['REQUEST_METHOD'] == 'get') {
  header('HTTP/1.0 400 Bad Request');
  exit('invalid method');
}

// retrieve parameters
$lang = $_GET['lang'];
if (!in_array($lang, array('fr', 'it', 'de', 'en', 'es', 'ca'))) {
  header('HTTP/1.0 400 Bad Request');
  exit('invalid lang');
}

$q = $_GET['name_startsWith'];
$callback = $_GET['callback'];

$url = 'http://api.geonames.org/searchJSON?maxRows=10&featureClass=P&featureClass=T&username=c2corg&lang=' .
       $lang . '&name_startsWith=' . urlencode($q) . '&callback=' . $callback;

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 5); 

$result = curl_exec($ch);

if (curl_errno($ch)) {
  header('HTTP/1.0 520');
  exit(curl_error($ch));
}

$status = curl_getinfo($ch)['http_code'];

curl_close($ch);

if ($status !== 200) {
  header('HTTP/1.0 ' . $status);
  exit('an error occured');
}

header('Content-type: application/javascript');
print $result;
