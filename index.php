<?php

include('common.php');


// Standard validated index page

if ($APIKeyValid == "true"){

echo '
  <div indent>
    <p>
This tool leverages the SparkPost API, stored recipients and stored templates.  No recipient data is stored within this tool.</p>
    <p>
If you elect to store log-term detailed data and provide an external datastore location, you are responsible for the data security of the external location.
    </p>
    <p>
APIs used by this application include Templates, Recipients, Metrics, Events, Webhooks.
   </p>

  </div>
';

echo '<a href="./lists.php">Click here to edit your lists </a>';

}


include('footer.php');


