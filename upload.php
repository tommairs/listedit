<?php


include('common.php');
$now = time();

$dup = 0;
$dupreplace = 0;
$dupreplace = $_POST['dataoverride'];

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
    if ($lines != "email,fullname,metadata,substitution_data"){
      echo "Invalid input file.  Table must be in this format:<br>email,fullname,metadata,substitution_data<br>";
      echo "JSON data must be enclosed in quotes.  IE: <br>email,fullname,metadata,substitution_data<br>
            tmairs@here.com,Tom Mairs,\"{'this':'that','here':'there'}\",\"{'first_name':'Tom','last_name':'Mairs'}\"<br>";
    }
    $csv = array();
    $headers = array();
    $importdata = array();
    $lines = file($target_file, FILE_IGNORE_NEW_LINES);
    $i = 0;
    foreach ($lines as $key => $value){
      if ($i<1){
       $headers[$key] = str_getcsv($value);
       if (($headers[0][0] == "email") && ($headers[0][1] == "fullname") && ($headers[0][2] == "metadata") 
               && ($headers[0][3] == "substitution_data")){
         echo "Found  good file format - Continuing...<br>";
       }
       else {
         echo "this file has bad headers - try again";
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



   foreach ($csv as $a => $b){
      foreach ($b as $k => $v){
        echo "<td> $v </td>";
      }
    echo "</tr>";
    }

    echo "</tr></table>";

if ($dupreplace == 1 ){
 echo "you have chosed to overwrite existing data <br>";
}
else{
 echo "Ignoring any duplicate data <br>";
}

  foreach ($csv as $a => $b){
    if($b[0] != "email"){
     $query = "INSERT INTO MyContacts (email, name, meta, subs) VALUES (:P1, :P2, :P3, :P4)";
     $query_params = array(
              ':P1' => $b[0],
              ':P2' => $b[1],
              ':P3' => $b[2],
              ':P4' => $b[3]       
        );
        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }

        catch(PDOException $ex)

       {
           // die("Failed to run query: " . $ex->getMessage());
            echo "Failed to add record for $b[0], address already exists.<br>";
            $dup = 1;
        }
    }
     
    if ($dup == 1){
      if ($dupreplace == 1){
        echo "Override set - replacing data for $b[0] <br>";

        $query = "UPDATE MyContacts SET name = :P2, meta = :P3, subs = :P4 WHERE email = :P1";
        $query_params = array(
              ':P1' => $b[0],
              ':P2' => $b[1],
              ':P3' => $b[2],
              ':P4' => $b[3]
        );
        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }

        catch(PDOException $ex)

       {
            die("Failed to run query: " . $ex->getMessage());
        }
      }
      else {
        echo "Duplicate NOT replaced <br>";
      }   
      $dup = 0;
    }


  }


  echo '
    <input type=button name=btn value="Click here to continue" onclick="location.href=\'index.php\';"><br>
  ';






  }


// Handle the JSON file type

  if ($FileType == "json"){
    $csv = json_decode($MyFilesContents, true);
    echo '<pre>';
    print_r($csv);
    echo '</pre>';

  }

 

/*
    $file = fopen($target_file, 'r');
    $r=1;
    $foundHdr = 0;
    $csv = array();
    while (($line = fgetcsv($file)) !== FALSE) {
  //    print_r($line);
      foreach ($line as $k => $v){
echo "$k => $v <br>";

          if (($r == 1) && (strtolower($v) == "email")){
            $foundHdr = 1;
            echo " This is a valid file to import...<br>";
          }
      }

        if ($foundHdr != 1){
          echo " This is NOT a valid file :(<br>Exiting importer<br>";
          exit;
          // Replace this with a graceful exit
        }
*/



   
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
      $_SESSION['templateTEXT'] = $filecontents;
    }
    echo "Click <input type=button value=here onClick=\"window.location.href='./quill.php';\"> to continue on to the editor and save.";
  }

}
