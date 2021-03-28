<?php

include('common.php');

$postarray = $_POST;


if ($postarray['SPRecipients'] == ""){

echo "There seems to be an error.  No list was found to edit.";
echo '<form action="index.php" method="post">
  <input type="submit" value="Go Home" name="submit"> and start over.
</form>
';


}

var_dump($_POST);



echo "<h2>WORKING....</h2><br>";

foreach ($postarray as $a => $b){
 if ($b != ""){
  if (substr($a,0,6) == "delcmd"){
    $removals[] = $b;
  }
  if (substr($a,0,7) == "newname"){ 
    $additions[] = $b;
  } 
  if (substr($a,0,8) == "newemail"){
    $addresses[] = $b;
  }
  if (substr($a,0,7) == "newtags"){
    $tags[] = $b;
  }
  if (substr($a,0,7) == "newmeta"){
    $meta[] = $b;
  }
  if (substr($a,0,7) == "newsubs"){
    $subs[] = $b;
  }
 }
}



echo "removals: <br>";
var_dump ($removals);
echo "<br>";

echo "additions: <br>";
var_dump ($additions);
echo "<br>";

echo "addresses: <br>";
var_dump ($addresses);
echo "<br>";

echo "tags: <br>";
var_dump ($tags);
echo "<br>";

echo "meta: <br>";
var_dump ($meta);
echo "<br>";

echo "subs: <br>";
var_dump ($subs);
echo "<br>";



echo "Pulling list data....<br>";


// Get the list details

$url = "https://".$apidomain."/api/v1/recipient-lists/".$postarray['SPRecipients']."?show_recipients=true";
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

$recipData = json_decode($response,true);

// Start building new array

echo "Appliying edits....<br>";

// apply removals
  $counter=0;
foreach($recipData['results']['recipients'] as $k => $v){
  foreach($v as $x => $y){
echo "counter: $counter <br>";

    if (in_array($y['email'] ,$removals)){
      echo "Removing ". $y['email']."<br>";
echo "full record:<br>";
var_dump($recipData['results']['recipients'][$counter]);
      unset($recipData['results']['recipients'][$counter]);
    }
  }
      $counter++;
}

// apply additions

$z=intval(sizeof($additions));

echo "<br>";
echo "additions: ($z)<br>";
echo "<br> Preserving what was there... <br>";

if (($z > 0) or ($counter > 0)){

$recordName = $recipData['results']['name'];
$recordDescription = $recipData['results']['description'];
$recordRecipients = $recipData['results']['recipients'];


// =============================================================

$NewJSONArray = '{
  "name": "'.$recordName.'",
  "description": "'.$recordDescription.'",
  "recipients": [';

foreach($recordRecipients as $x => $y){

//$y[tags] = array("my","own","tag","list");


echo "<br>Current record: ";
var_dump ($y);


  $NewJSONArray .= '{
      "address": {
        "email": "'. $y[address][email] .'",
        "name": "'. $y[address][name] .'"
      }';

    $NewJSONArray .= ', "tags": [';
    foreach($y[tags] as $tloop){
          $NewJSONArray .= '"'.$tloop.'",';
    }
    $NewJSONArray=trim ($NewJSONArray,",");
    $NewJSONArray .= '] ';

    $NewJSONArray .= ', "metadata": {';
    foreach($y[metadata] as $key => $mloop){
          $NewJSONArray .= '"'.$key.'":"'.$mloop.'",';
    }
    $NewJSONArray=trim ($NewJSONArray,",");
    $NewJSONArray .= '} ';

    $NewJSONArray .= ', "substitution_data": {';
    foreach($y[substitution_data] as $key => $sloop){
          $NewJSONArray .= '"'.$key.'":"'.$sloop.'",';
    }
    $NewJSONArray=trim ($NewJSONArray,",");
    $NewJSONArray .= '} ';

  $NewJSONArray .= ' },';
}

var_dump($NewJSONArray);

echo "<br> === New records == <br>";

//Add additions here...

if($z > 0){
  for ($x = 0; $x <= $z; $x++) {
    $counter = $counter + $x;
   $addressNext = preg_replace('/[\s]/',"",array_pop($addresses));
 //   $addressNext = array_pop($addresses);
    $additionNext = array_pop($additions);
    $tagsNext = array_pop($tags);
    $metaNext = array_pop($meta);
    $subsNext = array_pop($subs);

    if (!preg_match_all("/[a-zA-z0-9.-]+\@[a-zA-z0-9.-]+.[a-zA-Z]+/",$addressNext)){
      $addressNext = "";
    }
    if ($addressNext != ""){
      echo "Adding $addressNext <br>";

      $NewJSONArray .= '{
        "address": {
          "email": "'. $addressNext .'",
          "name": "'. $additionNext .'"
        }';

      if($tagsNext !=""){
        $NewJSONArray .= ', "tags": [';

    $tagsNextLoop = explode(",",$tagsNext);

    foreach($tagsNextLoop as $tloop){
          $NewJSONArray .= '"'.$tloop.'",';
    }
    $NewJSONArray=trim ($NewJSONArray,",");
    $NewJSONArray .= ' ] ';
      }

     if($metaNext !=""){
        $NewJSONArray .= ',"metadata": {';

//.$metaNext.'], ';
    $metaNextLoop = explode(",",$metaNext);

    foreach($metaNextLoop as $mloop){
      $varsplit=explode(":",$mloop);
          $NewJSONArray .= '"'.$varsplit[0].'":"'.$varsplit[1].'",';
    }
    $NewJSONArray=trim ($NewJSONArray,",");
    $NewJSONArray .= '} ';

      }

     if($subsNext !=""){
        $NewJSONArray .= ',"substitution_data": {';
//.$subsNext.'], ';
    $subsNextLoop = explode(",",$subsNext);

    foreach($subsNextLoop as $sloop){
      $varsplit=explode(":",$sloop);
          $NewJSONArray .= '"'.$varsplit[0].'":"'.$varsplit[1].'",';
    }
    $NewJSONArray=trim ($NewJSONArray,",");
    $NewJSONArray .= '} ';


      }
/*
      if($meta !=""){
        $NewJSONArray .= '       "metadata": {
          "age": "24",
          "place": "Bedrock"
        }, ';
      }

      if($subs !=""){
        $NewJSONArray .= '      "substitution_data": {
          "favorite_color": "SparkPost Orange",
          "job": "Software Engineer"
        }';
      }
*/

  $NewJSONArray .= ' },';
    }
  }
}

  $NewJSONArray = trim($NewJSONArray,',');
  $NewJSONArray .= ' ]}';

echo "This is the new JSON Array to post:<br>";
var_dump($NewJSONArray);


// =============================================================
$json = $NewJSONArray;

echo "<pre><br>";
var_dump($json);
echo "</pre><br>";


$url = "https://".$apidomain."/api/v1/recipient-lists/".$postarray['SPRecipients']."";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
//curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "Authorization: $apikey"
));
$response = curl_exec($ch);
curl_close($ch);

$res = json_decode($response,true);

echo "<pre><br>";
var_dump($res);
echo "</pre><br>";

echo "error: ".$res['errors'][0]['message']."<br>";

if ($res['errors'][0]['message'] == "At least one valid recipient is required"){
  echo "Going to remove entire store list<br>";

$url = "https://".$apidomain."/api/v1/recipient-lists/".$postarray['SPRecipients']."";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
//curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "Authorization: $apikey"
));
$response = curl_exec($ch);
curl_close($ch);

$res = json_decode($response,true);




}

echo "DONE <br>";

}
  echo "Returning now...";
    echo '<script type="text/javascript">
            window.open("lists.php?job=","_self");
          </script>';



?>

