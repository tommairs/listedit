<?php

include('common.php');

$listID = $_POST['SPRecipients'];

// Handle the new list use case
if ($listID == ""){


echo "Upload a new CSV file. One field must be named EMAIL and include addresses.<br>";
echo '<form action="upload.php" method="post" enctype="multipart/form-data">
 <b>NOTE</b> - Any file uploaded has the potential to overwrite an entire list.  Be careful with your list names.<br>
<br>
  Select CSV file to upload:
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="submit" value="Upload" name="submit">
</form>
';

exit;

}


  // Get the list details

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

  echo "<b>List id</b>: ".$recipData['results']['id']." <br>";
  echo "<b>List name</b>: ".$recipData['results']['name']." <br>";
  echo "<b>List count</b>: ".$recipData['results']['total_accepted_recipients']." <br>";
  echo "<form name=f2 action=\"./editlist.php\" method=\"post\">";
  echo "<input type=hidden name=SPRecipients value=".$listID.">";
  echo "<table border=1><tr><th>NAME</th><th>Email</th><th>Tags</th><th>Metadata</th><th>Substitutions</th><th>DEL</th></tr>";

  $counter=0;
  foreach($recipData['results']['recipients'] as $k => $v){
    $v_tags = "";
    foreach($v['tags'] as $t => $r){
      $v_tags .= "$r,";
    }
    $v_tags = trim($v_tags,",");

    $v_meta = "";
    foreach($v['metadata'] as $t => $r){
      $v_meta .= "$t:$r,";
    }
    $v_meta = trim($v_meta,",");

    $v_subs = "";
    foreach($v['substitution_data'] as $t => $r){
      $v_subs .= "$t:$r,";
    }
    $v_subs = trim($v_subs,",");

    foreach($v as $x => $y){

      if (strlen($y['email']) > 3 ){
        echo "<tr><td>". $y['name'] ."</td><td>". $y['email'] ."</td>
          <td>". $v_tags ."</td>
          <td>". $v_meta ."</td>
          <td>". $v_subs ."</td>
          <td><input type=\"checkbox\" id=\"delcmd".$counter."\" name=\"delcmd".$counter."\" value=\"". $y['email'] ."\"></td></tr>";
        $counter++;
      }
    }
  }
  echo "<tr>
        <td><input type=text size=30 name=newname1></td>
        <td><input type=text size=30 name=newemail1></td>
        <td><input type=text size=30 name=newtags1></td>
        <td><input type=text size=30 name=newmeta1></td>
        <td><input type=text size=30 name=newsubs1></td>
        <td>ADD</td>
     </tr>";
  echo "<tr>
        <td><input type=text size=30 name=newname2></td>
        <td><input type=text size=30 name=newemail2></td>
        <td><input type=text size=30 name=newtags2></td>
        <td><input type=text size=30 name=newmeta2></td>
        <td><input type=text size=30 name=newsubs2></td>
        <td>ADD</td>
     </tr>";
  echo "<tr>
        <td><input type=text size=30 name=newname3></td>
        <td><input type=text size=30 name=newemail3></td>
        <td><input type=text size=30 name=newtags3></td>
        <td><input type=text size=30 name=newmeta3></td>
        <td><input type=text size=30 name=newsubs3></td>
        <td>ADD</td>
     </tr>";
  echo "<tr>
        <td><input type=text size=30 name=newname4></td>
        <td><input type=text size=30 name=newemail4></td>
        <td><input type=text size=30 name=newtags4></td>
        <td><input type=text size=30 name=newmeta4></td>
        <td><input type=text size=30 name=newsubs4></td>
        <td>ADD</td>
     </tr>";
  echo "<tr>
        <td><input type=text size=30 name=newname5></td>
        <td><input type=text size=30 name=newemail5></td>
        <td><input type=text size=30 name=newtags5></td>
        <td><input type=text size=30 name=newmeta5></td>
        <td><input type=text size=30 name=newsubs5></td>
        <td>ADD</td>
     </tr>";
  echo "<tr><td colspan=6><input type=\"submit\" value=\"UPDATE\" name=\"submit\"></td></tr>";
  echo "</table></form>";


  echo '
    <form method=post action=filedl.php>
    <input type=hidden name=listID value="'.$listID.'">
    <input type=submit name=sbt value="Click here to download this list as a CSV file.">
    </form>
  ';



