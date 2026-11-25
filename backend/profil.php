
<?php

session_start();
require "datenbank.php";

// check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    die("Please log in first");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION["user_id"];
    $name = trim($_POST["display_name"] ?? "");
    $bio = trim($_POST["bio"] ?? "");
    $city = trim($_POST["city"] ?? "");

    // check the display name
    if ($name == "" || strlen($name) > 100) {
        die("Please enter a valid display name");
    }

    // save or update the profile
    $sql = "INSERT INTO profiles
            (user_id, display_name, bio, city)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            display_name = VALUES(display_name),
            bio = VALUES(bio),
            city = VALUES(city)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $name, $bio, $city);

    $stmt->execute();
    $stmt->close();

    // return to profile
    header("Location: ../frontend/profil.php");
    exit;
}
