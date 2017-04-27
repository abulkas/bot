<?php
require ('sdk.class.php');

//new SDK object
$sdk = new SDK("EAAaZApd9AGQEBAPtFJZCV3BddiE3GyUxpvMEzZB3qzDMiGxi6XtoJf5tJPgCByLHkPsGt04TDhE4DOelCOhrd"
        . "9TRL8S2f6vAZAzNjQQYZCpEfQQLIr15ZBfh6mQ9yTKdiyTHmj5awbVFYpOONk2wZCzq3RvGN3S37Ahqod9QFf3DgZDZD"
        , "abulkas");

$result = $sdk->getNearestHotels("32.271957", "35.890543");
?>
<pre>
    <?php print_r($result);
    ?>

</pre>
