<?php

include('common.php');
$now = time();


$target_dir = "/var/www/html/cst/Ramesses/uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));


// Check if file is valid
if(isset($_POST["submit"])) {
  // Allow certain file formats
  if($FileType != "csv" && $FileType != "html" && $FileType != "txt" && $FileType != "json") {
    echo "Sorry, only CSV, JSON, HTML, and TXT  files are allowed.";
    $uploadOk = 0;
    echo "<br><input type='button' value='Close'  onclick='history.back(-1)'> ";
    exit(0);
  }
}
if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target_file)) {
    echo "File ". $_FILES["fileToUpload"]["name"]. " was succesfully uploaded. <br>";
} else {
    echo "Possible file upload attack!<br>";
    echo "This incident has been logged.<br>";
    // Write Log Line

    echo "<br><input type='button' value='Close'  onclick='history.back(-1)' >";
    exit(0);
}
if ($uploadOk == 1){
  if ($FileType == "json" || $FileType == "csv"){
    echo "Importing your data file...<br>";
    echo '<img src="./res/waitgears.gif" alt="Please wait timer..." width="78" height="78">';
    $MyFileContents = file_get_contents($target_file);

    echo"<p>.... Checking input file: <br>";
  //  echo "<pre><br><font color=red>".$MyFileContents."</font><br></pre>";
    echo "</p>";

/********************************************************************************/
// read CSV into array
// isolate the headers
// check to see if one is named 'email'
// fail if not
// proceed if true
// create new table
// load data to new table
// show results
// preset 'proceed' button
// CSV MUST be in this format:
//   email,fullname,metadata,substitution_data
// JSON MUST be in this format:

/*
{
  "address": {
    "email": "fred.jones@flintstone.com",
    "name": "Grad Student Office"
  },
  "tags": [
    "driver",
    "fred",
    "flintstone"
  ],
  "metadata": {
    "age": "33",
    "place": "NY"
  },
  "substitution_data": {
    "favorite_color": "Bright Green",
    "job": "Firefighter"
  }
}

*/
/*****************************************************************************/


// Handle the CSV file type

  if ($FileType == "csv"){
    $lines = strtok($MyFileContents, "\n") ;
    if (substr($lines,0,17) != "email,name,return"){
      echo "Invalid input file.  Table must be in this format:<br>email,name,return_path,metadata,substitution_data,tags<br>";
      echo "JSON data must be enclosed in quotes.  IE: <br>email,name,return_path,metadata,substitution_data,tags<br>
            tmairs@here.com,Tom Mairs,,\"{'this':'that','here':'there'}\",\"{'first_name':'Tom','last_name':'Mairs'}\",<br>";
      echo " This is what I got: <br>";
      echo $lines ."<br>";
    }
    $csv = array();
    $headers = array();
    $importdata = array();
    $lines = file($target_file, FILE_IGNORE_NEW_LINES);
    $i = 0;
    foreach ($lines as $key => $value){
      if ($i<1){
       $headers[$key] = str_getcsv($value);
       if (($headers[0][0] == "email") && ($headers[0][1] == "name") && ($headers[0][3] == "metadata") 
               && ($headers[0][4] == "substitution_data")){
         echo "Found  good file format - Continuing...<br>";
       }
       else {
         echo "this file has bad headers - try again<br>";
         exit;
       }

       echo"<p>.... File content preview: <br>";

      }
      $csv[$key] = str_getcsv($value);
      $i++;
    }

    echo "<table border = 1 cellpadding=5><tr>";

    foreach ($headers as $a => $b){
      foreach ($b as $k => $v){
        $importdata[$v]="";
      //  echo "<th> $v </th>";
      }
    }

   $pcount = 0;
     foreach ($csv as $a => $b){
        foreach ($b as $k => $v){
          echo "<td> $v </td>";
        }
      echo "</tr>";
          $pcount ++;
          if ($pcount > 11){
            break;
          }
      }

    echo "</tr></table>";
   $remainder = sizeof($csv) - 10;
   echo "... and $remainder more rows.<br>";

// write data to JSON
  $NewJSONArray = ""; 
  foreach ($csv as $a => $b){


    if (implode($b) != "emailnamereturn_pathmetadatasubstitution_datatags"){
  
    $NewJSONArray .= '{
      "address": {
        "email": "'. $b[0] .'",
        "name": "'. $b[1] .'"
      }';

    $NewJSONArray .= ', "tags": [';
    foreach($b[5] as $tloop){
          $NewJSONArray .= '"'.$tloop.'",';
    }
    $NewJSONArray=trim ($NewJSONArray,",");
    $NewJSONArray .= '] ';

    $NewJSONArray .= ', "metadata": {';
    foreach($b[3] as $key => $mloop){
          $NewJSONArray .= '"'.$key.'":"'.$mloop.'",';
    }
    $NewJSONArray=trim ($NewJSONArray,",");
    $NewJSONArray .= '} ';

    $NewJSONArray .= ', "substitution_data": {';
    foreach($b[4] as $key => $sloop){
          $NewJSONArray .= '"'.$key.'":"'.$sloop.'",';
    }
    $NewJSONArray=trim ($NewJSONArray,",");
    $NewJSONArray .= '} ';

  $NewJSONArray .= ' },';

    }
  }
  $NewJSONArray = trim($NewJSONArray,",");

// Save the data as a Session Var
$_SESSION['toothbrush'] = $NewJSONArray;


/****************************************************/
// Get the current list of lists
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

// End list of lists selection
/****************************************************/
 
echo '<font color=red><b> WARNING</b> - chosing to REPLACE a list is irreverable and permanent!</font><br>';
  echo '
    <form method=POST action="savelist.php">

    Replace this list: 

    <select name="SPRecipients" id="SPRecipients">
      <option value=X selected>DO NOT REPLACE</option>
  ';

  foreach($reciplistArray as $a=>$b){
    foreach ($b as $c=>$d){
      echo "<option value=$d[id] >$d[name]</option>";
    }
  }

  echo '</select><br>

    ... OR ... <br>
    Create New list named: <input type=text name=newlistname value="" placeholder="My New List 12345">
    <input type=submit name=btnSubmit value="Save"> &nbsp;
    <input type=reset onclick="location.href = \'lists.php\';"> 
    </form> 
 ';

  }

// Handle the JSON file type

  if ($FileType == "json"){
    $csv = json_decode($MyFilesContents, true);
    echo '<pre>';
    print_r($csv);
    echo '</pre>';

  }

  }
  if ($FileType == "html" || $FileType == "txt"){
    echo "Importing your $FileType template...";
    $filecontents = file_get_contents($target_file);
    if (preg_match('/(<.*>)/i', $filecontents)) {
        $FileType = "html";
    }


    if ($FileType == "html"){
      $_SESSION['templateHTML'] = $filecontents;
    }
    if ($FileType == "txt"){
      $_SESSION['templateTEXT'] = $filecontents; } echo "Click <input type=button value=here onClick=\"window.location.href='./quill.php';\"> to continue on to the editor and save.";
  }

}
