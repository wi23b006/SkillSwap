
<?php

require "datenbank.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $privacy = $_POST["privacy_accepted"] ?? "";

    // check user input
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email address");
    }

    if (strlen($password) < 8) {
        die("Password must have at least 8 characters");
    }

    if ($privacy != "1") {
        die("Privacy consent is required");
    }

    // password hashen
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // neuer benutzer wird in der datenbank gespeichert
    $sql = "INSERT INTO users
            (email, password_hash, privacy_accepted_at, privacy_policy_version)
            VALUES (?, ?, NOW(), ?)";

    $stmt = $conn->prepare($sql);
    $version = "draft-1";
    $stmt->bind_param("sss", $email, $hash, $version);

    try {
        $stmt->execute();
        echo "Registration successful!";
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            echo "Email already registered";
        } else {
            error_log($e->getMessage());
            echo "Registration failed";
        }
    }

    $stmt->close();
}
