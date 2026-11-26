
<?php

session_start();
require "datenbank.php";
require "cometchat.php";

// check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../frontend/login.html#login-required");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION["user_id"];
    $name = trim($_POST["display_name"] ?? "");
    $bio = trim($_POST["bio"] ?? "");
    $city = trim($_POST["city"] ?? "");

    // check the display name
    if ($name == "" || mb_strlen($name) > 100) {
        die("Please enter a valid display name");
    }

    // check if the user already has a profile
    $stmt = $conn->prepare(
        "SELECT id FROM profiles WHERE user_id = ?"
    );

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $has_profile = $stmt->get_result()->num_rows > 0;

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

    // create a CometChat account for new profiles
    if (!$has_profile) {

        $created = create_cometchat_user($conn, $user_id);

        if (!$created) {
            error_log("CometChat user creation failed: " . $user_id);
        }
    }

    // return to profile
    header("Location: ../frontend/profil.php");
    exit;
}
