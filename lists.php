<?php

include('common.php');

$url = "https://".$apidomain."/api/v1/recipient-lists";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_POST, FALSE);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "Authorization: $apikey"
));
$response = curl_exec($ch);
curl_close($ch);

$reciplistArray = json_decode($response,true);

echo '
<p>
<form name="f1" method="POST" action="./dblist.php">
Recipient Lists stored in SparkPost<br>
<select name="SPRecipients" id="SPRecipients">
';

foreach($reciplistArray as $a=>$b){
  foreach ($b as $c=>$d){
      echo "<option value=$d[id] selected>$d[name]</option>";
  }
}

echo '
</select>
<button type="submit">Open</button> &nbsp;
</p>';

echo '
<p><button type="button" onClick="window.location.href=\'./dblist.php\';">Import/Upload from file</button></p>
</p>';


?>


