<?php

  // If not using HTTPS, bail
  if (($_SERVER['REMOTE_PORT']) AND ($_SERVER['SERVER_PORT'] != "443")){
    echo "Please use HTTPS";
    exit;
  }

  include('env.php');
  date_default_timezone_set($TZ);
  $now = time();
  $onehourago = $now - 3600;
  $lastmonth = $now - (30*24*3600);

  include('header.php');

  $apiroot = "https://".$apidomain."/api/v1";

  if ($_SESSION['apikey'] !=""){
    $apikey=$_SESSION['apikey'];
  }
  if ($_POST['apikey'] !=""){
    $apikey=$_POST['apikey'];
    $_SESSION['apikey'] = $apikey;
  }

  $APIKeyValid = "false";

  if (strlen($apikey) > 39){
    $APIKeyValid = "true";
  }

  // If we have the keys, go for a drive right away... Otherwise, ask for it nicely.
  if (strlen($apikey) < 39){
    $apikey = $_SESSION['apikey'];
    if (strlen($apikey) < 39){

      echo "<br>Invalid API key.  Update the environment file or enter one below and retry.<br>
                This will be saved as a PHP Sesssion, so you may want to clear your cookies when you are done.<br>";

      echo '<form name=fm1 method=post>
              <input size=50 type=password name=apikey value="">
              <input type=submit>
            </form>';


      echo '
         <table border="0" width="75%" cellpadding="20">
         <tr>
           <td>
            Note: These API Keys are created from the admin page within your SparkPost account at
            <a href="https://app.sparkpost.com/account/credentials">
            https://app.sparkpost.com/account/credentials</a>.
            Please remember that the SparkPost system only shows your API Key "once", so you need
            to keep the API Key safe where you can get to it each time you use this or any application
            that needs an API Key.  If you loose the API Key you can always create a new one.<p>

            At a minimum, you need to select \'Recipient Lists: Read/Write, Templates: Read/Write,
              Transmissions: Read/Write and Sending Domains: Read\'.
           </td>
         </tr>
         </table>
      ';

      exit;
   }
  }







