<?php
$ch = curl_init();
  curl_setopt($ch,CURLOPT_ENCODING,""); 
  curl_setopt($ch,CURLOPT_URL,"https://analogio.dk/publicshiftplanning/api/shifts/analog");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
  $content = curl_exec($ch);
  curl_close($ch);
  header('Content-Type: application/json');
  //$shifts = str_replace("open", "Open", str_replace("close", "Close", str_replace("employees", "Employees", $content)));
  
  exit($content);
?>