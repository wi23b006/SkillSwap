
<?php

// only run this test in the terminal
if (PHP_SAPI != "cli") {
    exit("Run this test in the terminal.");
}

require __DIR__ . "/../backend/datenbank.php";
require __DIR__ . "/../backend/cometchat.php";

// connect Michael and Lucia using match ID 1
$result = sync_cometchat_match($conn, 1);

if ($result) {
    echo "CometChat friendship created successfully!";
} else {
    echo "CometChat friendship failed!";
}
