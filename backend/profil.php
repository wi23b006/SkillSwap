<?php

session_start();
require "datenbank.php";

// check ob user logged in ist
if (!isset($_SESSION["user_id"])) {
    die("Please log in first");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION["user_id"];
    $name = trim($_POST["display_name"] ?? "");
    $bio = trim($_POST["bio"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $email = trim($_POST["contact_email"] ?? "");

    // check input
    if ($name == "" || strlen($name) > 100) {
        die("Please enter a valid display name");
    }

    if ($email != "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid contact email");
    }

    // profil in der datenbank speichern oder aktualisieren
    $sql = "INSERT INTO profiles
            (user_id, display_name, bio, city, contact_email)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            display_name = VALUES(display_name),
            bio = VALUES(bio),
            city = VALUES(city),
            contact_email = VALUES(contact_email)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $user_id, $name, $bio, $city, $email);

    $stmt->execute();

    echo "Profile saved successfully!";

    $stmt->close();
}
