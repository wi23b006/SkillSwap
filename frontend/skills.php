<?php
session_start();
require "../backend/datenbank.php";

// only for logged USers
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

// offered or wanted? 
$type = ($_GET["type"] ?? "") == "wanted" ? "wanted" : "offered";
$title = $type == "offered" ? "Add a skill you can offer" : "Add a skill you want to learn";

// load all approved skills from the catalogue
$result = $conn->query("SELECT id, name FROM skills WHERE status = 'approved' ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?> – SkillSwap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">

    <!-- navigation (logged in) -->
    <nav class="navbar bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand fw-semibold fs-4" href="index.html">⇄ SkillSwap</a>
            <div class="d-flex gap-4">
        
                <a class="nav-link" href="profil.php">My Profile</a>
                <a class="nav-link" href="../backend/logout.php">Log out</a>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="card col-md-8 col-lg-6 col-xl-5 mx-auto">
            <div class="card-body p-4 p-md-5">

                <!-- dot color -->
                <h1 class="h3 fw-semibold"><span class="dot dot-<?= $type ?>"></span><?= $title ?></h1>
                <p class="text-body-secondary mb-4">Choose from existing skills in the catalogue.</p>

                <!-- sends skill to the backend -->
                <form action="../backend/skills.php" method="POST">

                    <input type="hidden" name="skill_type" value="<?= $type ?>">

                    <div class="list-group mb-4">
                        <?php while ($skill = $result->fetch_assoc()): ?>
                            <label class="list-group-item">
                                <input type="radio" name="skill_id" value="<?= $skill["id"] ?>" class="form-check-input radio-<?= $type ?> me-2" required>
                                <?= htmlspecialchars($skill["name"]) ?>
                            </label>
                        <?php endwhile; ?>
                    </div>

                    <button type="submit" class="btn btn-<?= $type ?> rounded-pill w-100 py-2">Add skill</button>
                </form>

                <p class="text-center mt-4 mb-0">
                    <a href="profil.php">Back to my profile</a>
                </p>

            </div>
        </div>
    </main>

</body>
</html>
