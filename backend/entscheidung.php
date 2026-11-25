
<?php

session_start();
require "datenbank.php";
require "matching.php";

// check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../frontend/login.html#login-required");
    exit;
}

// get the logged in user and the decision
$user_id = $_SESSION["user_id"];
$other_id = filter_input(INPUT_POST, "user_id", FILTER_VALIDATE_INT);
$decision = $_POST["decision"] ?? "";

// check if the input is valid
if (!$other_id || !in_array($decision, ["like", "pass"])) {
    header("Location: ../frontend/match.php#decision-error");
    exit;
}

// check if my profile exists
$stmt = $conn->prepare(
    "SELECT id FROM profiles WHERE user_id = ?"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

if ($stmt->get_result()->num_rows == 0) {
    header("Location: ../frontend/match.php#decision-error");
    exit;
}

// only allow decisions for suggested users
$suggestions = get_suggestions($conn, $user_id);
$allowed = array_column($suggestions, "user_id");

if (!in_array($other_id, $allowed)) {
    header("Location: ../frontend/match.php#decision-error");
    exit;
}

// save like or pass in the database
$stmt = $conn->prepare(
    "INSERT INTO decisions (from_user_id, to_user_id, decision)
     VALUES (?, ?, ?)"
);

$stmt->bind_param("iis", $user_id, $other_id, $decision);
$stmt->execute();


// check if the other user liked me too
if ($decision == "like") {

    $stmt = $conn->prepare(
        "SELECT id FROM decisions
         WHERE from_user_id = ?
         AND to_user_id = ?
         AND decision = 'like'"
    );

    $stmt->bind_param("ii", $other_id, $user_id);
    $stmt->execute();

    $result = $stmt->get_result();

    // both users liked each other
    if ($result->num_rows > 0) {

        // save the smaller user id first
        $user1 = min($user_id, $other_id);
        $user2 = max($user_id, $other_id);

        // create the match
        $stmt = $conn->prepare(
            "INSERT IGNORE INTO matches (user1_id, user2_id)
             VALUES (?, ?)"
        );

        $stmt->bind_param("ii", $user1, $user2);
        $stmt->execute();

        // show match notification
        header("Location: ../frontend/match.php#new-match");
        exit;
    }
}

// return to discover
header("Location: ../frontend/match.php");
exit;
