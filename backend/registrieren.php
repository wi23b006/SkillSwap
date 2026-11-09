<?php

require "datenbank.php";

// zurück zur registrierung
function back_with_error($id) {
    header("Location: ../frontend/registrierung.html#" . $id);
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $privacy = $_POST["privacy_accepted"] ?? "";

    // check user input
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email address");
    }

    if (strlen($password) < 8) {
        back_with_error("reg-error");
    }

   / beide passwörter müssen gleich sein
    if ($password !== ($_POST["password_confirm"] ?? "")) {
        back_with_error("reg-password-mismatch");
    }

    if ($privacy != "1") {
        back_with_error("reg-error");
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

        // weiter zum login
        header("Location: ../frontend/login.html#registered");
        exit;
     } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            back_with_error("reg-email-taken");
        } else {
            error_log($e->getMessage());
            back_with_error("reg-error");
        }
    }
}
