<?php
include('common.php');

  $listID = $_POST['listID'];

  // output headers so that the file is downloaded rather than displayed
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename=data.csv');

  // create a file pointer connected to the output stream
  $output = fopen('php://output', 'w');

  // fetch the data  
  $url = "https://".$apidomain."/api/v1/recipient-lists/".$listID."?show_recipients=true";
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

 

  echo "List id: ".$recipData['results']['id']." \n";
  echo "List name: ".$recipData['results']['name']." \n";
  echo "List count: ".$recipData['results']['total_accepted_recipients']." \n";
  echo "\n";

  echo "email,name,return_path,metadata,substitution_data,tags \n";

  $counter=0;
  foreach($recipData['results']['recipients'] as $k => $v){
    $v_tags = "";
    foreach($v['tags'] as $t => $r){
      $v_tags .= "\"\"$r\"\",";
    }
    $v_tags = trim($v_tags,",");

    $v_meta = "";
    foreach($v['metadata'] as $t => $r){
      $v_meta .= "\"\"$t\"\":\"\"$r\"\",";
    }
    $v_meta = trim($v_meta,",");

    $v_subs = "";
    foreach($v['substitution_data'] as $t => $r){
      $v_subs .= "\"\"$t\"\":\"\"$r\"\",";
    }
    $v_subs = trim($v_subs,",");

    foreach($v as $x => $y){
      if (strlen($y['email']) > 3 ){
        echo "". $y['email'] .",". $y['name'] .",". $y['return_path'] .",\"{". $v_meta ."}\",\"{". $v_subs ."}\",\"[". $v_tags ."]\"\n";
          $counter++;
      }
    }
  }

 
