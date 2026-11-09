<?php

session_start();
require "datenbank.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // find user by email
    $sql = "SELECT id, password_hash, user_role FROM users WHERE email = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // check password
    if ($user && password_verify($password, $user["password_hash"])) {

        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_role"] = $user["user_role"];

        // weiter zum profil
        header("Location: ../frontend/profil.php");
        exit;

    } else {
        // back to login, the error message is shown there
        header("Location: ../frontend/login.html#login-error");
        exit;
    }

    $stmt->close();
}
