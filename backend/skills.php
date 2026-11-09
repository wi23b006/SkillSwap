<?php

require "datenbank.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    session_start();

    // login checken
    if (!isset($_SESSION["user_id"])) {
        die("Please log in first");
    }

    $user_id = $_SESSION["user_id"];
    $skill_id = filter_input(INPUT_POST, "skill_id", FILTER_VALIDATE_INT);
    $skill_type = $_POST["skill_type"] ?? "";

    // input checken
    if (!$skill_id || !in_array($skill_type, ["offered", "wanted"])) {
        die("Invalid skill");
    }

    // abchecken ob skill approved ist
    $stmt = $conn->prepare("SELECT id FROM skills WHERE id = ? AND status = 'approved'");
    $stmt->bind_param("i", $skill_id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows == 0) {
        die("Skill not available");
    }

    $stmt->close();

    // skill speichern
    $sql = "INSERT IGNORE INTO user_skills (user_id, skill_id, skill_type)
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $user_id, $skill_id, $skill_type);
    $stmt->execute();
    $stmt->close();

    // zurück zum profil (skill ist jetzt dort als chip sichtbar)
    header("Location: ../frontend/profil.php");
    exit;

} else {

    // verfügbare skills holen
    $sql = "SELECT id, name FROM skills WHERE status = 'approved' ORDER BY name";
    $result = $conn->query($sql);

    $skills = [];

    while ($row = $result->fetch_assoc()) {
        $skills[] = $row;
    }

    header("Content-Type: application/json");
    echo json_encode($skills);
}

$conn->close();
