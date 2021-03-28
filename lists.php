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

/*
echo '
<p>
Local Stored Lists<br>
<select name="SPQueries" id="SPQueries">
  <option value="x" selected>Select a Query</option>
';

$query = "SELECT id, Name FROM MyQueries";
   $query_params = array(
              ':ID' => 1
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

  while ($row = $stmt->fetch()){
    echo "<option value=". $row['id'] ." selected>". $row['Name'] ."</option>";
  }

echo '
</select>
<button type="submit">Open</button> &nbsp;
</form>
';
*/


/*
echo '
<form method=post action="dbentry.php">
<br><p>...OR... Create New Query:</p>

<b>SELECT</b>
<select name="cf1">

';

$query = "Show Columns FROM MyContacts";
   $query_params = array(
              ':ID' => 1
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

  while ($row = $stmt->fetch()){
    echo "<option value=". $row['Field'] .">". $row['Field'] ."</option>";
  }


echo '
</select>


<b>, </b>
<select name="cf2">
  "<option value="none" ></option>

';
$query = "Show Columns FROM MyContacts";
   $query_params = array(
              ':ID' => 1 
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

  while ($row = $stmt->fetch()){
    echo "<option value=". $row['Field'] .">". $row['Field'] ."</option>";
  }


echo '
</select>


<b>, </b>
<select name="cf3">
  "<option value="none" ></option>

';

$query = "Show Columns FROM MyContacts";
   $query_params = array(
              ':ID' => 1 
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

  while ($row = $stmt->fetch()){
    echo "<option value=". $row['Field'] .">". $row['Field'] ."</option>";
  }


echo '
</select>



<b>, </b>
<select name="cf4">
  "<option value="none" ></option>

';

$query = "Show Columns FROM MyContacts";
   $query_params = array(
              ':ID' => 1 
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

  while ($row = $stmt->fetch()){
    echo "<option value=". $row['Field'] .">". $row['Field'] ."</option>";
  }


echo '
</select>

<b>FROM</b> <i>MyContacts</i> <b>WHERE</b> 
<select name="wh1">

';

$query = "Show Columns FROM MyContacts";
   $query_params = array(
              ':ID' => 1
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

  while ($row = $stmt->fetch()){
    echo "<option value=". $row['Field'] .">". $row['Field'] ."</option>";
  }


echo '
</select>
<select name="cond">
    <option value=" LIKE " selected> LIKE </option>
    <option value=" NOT LIKE " > NOT LIKE </option>
    <option value=" = "> = </option>
    <option value=" != "> != </option>
</select>

<input type=text name=textval value=""> 
LIMIT 
<input type=text name=limitval value="-1" size=5> 
<p><input type="submit" value="Create New List" name=btn4></p>
</form>
';

*/

echo '
<p><button type="button" onClick="window.location.href=\'./dblist.php\';">Import/Upload from file</button></p>
</p>';



?>


