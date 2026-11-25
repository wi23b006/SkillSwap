
<?php

session_start();
require "datenbank.php";

// check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../frontend/login.html#login-required");
    exit;
}

// get the logged in user
$user_id = $_SESSION["user_id"];

// get the skill to remove
$skill_id = filter_input(INPUT_POST, "skill_id", FILTER_VALIDATE_INT);
$skill_type = $_POST["skill_type"] ?? "";

// check if the input is valid
if (!$skill_id || !in_array($skill_type, ["offered", "wanted"])) {
    header("Location: ../frontend/profil.php#skill-error");
    exit;
}

// remove the skill from the user's profile
$stmt = $conn->prepare(
    "DELETE FROM user_skills
     WHERE user_id = ?
     AND skill_id = ?
     AND skill_type = ?"
);

$stmt->bind_param("iis", $user_id, $skill_id, $skill_type);
$stmt->execute();

// return to profile
header("Location: ../frontend/profil.php");
exit;
