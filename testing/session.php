<?php

session_start();

if (isset($_SESSION["user_id"])) {
    echo "User is logged in!";
    echo "<p>User ID: " . $_SESSION["user_id"] . "</p>";
} else {
    echo "User is not logged in!";
}
