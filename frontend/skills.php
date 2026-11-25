<?php
session_start();
require "../backend/datenbank.php";

// only for logged USers
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html#login-required");
    exit;
}

// active link in the navbar
$page = "profil";

// offered or wanted? 
$type = ($_GET["type"] ?? "") == "wanted" ? "wanted" : "offered";
$title = $type == "offered" ? "Add a skill you can offer" : "Add a skill you want to learn";

// load all approved skills from the catalogue
$result = $conn->query("SELECT id, name FROM skills WHERE status = 'approved' ORDER BY name");

// categories from our skill catalogue (Azure wiki)
$categories = [
    "Programming & IT" => ["Python", "JavaScript", "HTML & CSS", "SQL", "Git & GitHub"],
    "Languages" => ["English Conversation", "German Conversation", "Spanish Conversation", "French Conversation", "Italian Conversation"],
    "Design & Creativity" => ["Graphic Design", "UI/UX Design", "Photography", "Video Editing", "Drawing"],
    "Music" => ["Guitar", "Piano", "Singing", "Music Production", "Music Theory"],
    "Business & Office" => ["Microsoft Excel", "PowerPoint", "Public Speaking", "Project Management", "CV & Interview Skills"],
    "Everyday & Lifestyle" => ["Cooking", "Baking", "Fitness Training", "Yoga", "Gardening"],
];

// empty list for every category
$groups = [];
foreach ($categories as $category => $names) {
    $groups[$category] = [];
}

// put every skill into its category (new skills go to "Other")
while ($skill = $result->fetch_assoc()) {
    $group = "Other";

    foreach ($categories as $category => $names) {
        if (in_array($skill["name"], $names)) {
            $group = $category;
        }
    }

    $groups[$group][] = $skill;
}
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
<body class="bg-body-tertiary d-flex flex-column min-vh-100">

    <?php require "navbar.php"; ?>

    <main class="container py-5">
        <div class="card col-lg-10 col-xl-8 mx-auto">
            <div class="card-body p-4 p-md-5">

                <!-- dot color -->
                <h1 class="h3 fw-semibold"><span class="dot dot-<?= $type ?>"></span><?= $title ?></h1>
                <p class="text-body-secondary mb-4">Choose from existing skills in the catalogue.</p>

                <!-- sends skill to the backend -->
                <form action="../backend/skills.php" method="POST">

                    <input type="hidden" name="skill_type" value="<?= $type ?>">

                    <!-- one list per category -->
                    <div class="row g-4 mb-4">
                        <?php foreach ($groups as $category => $skills): ?>
                            <?php if (count($skills) > 0): ?>
                                <fieldset class="col-md-6">
                                    <legend class="h6 mb-2"><?= htmlspecialchars($category) ?></legend>
                                    <div class="list-group">
                                        <?php foreach ($skills as $skill): ?>
                                            <label class="list-group-item">
                                                <input type="radio" name="skill_id" value="<?= $skill["id"] ?>" class="form-check-input radio-<?= $type ?> me-2" required>
                                                <?= htmlspecialchars($skill["name"]) ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </fieldset>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <button type="submit" class="btn btn-<?= $type ?> rounded-pill w-100 py-2">Add skill</button>
                </form>

                <p class="text-center mt-4 mb-0">
                    <a href="profil.php">Back to my profile</a>
                </p>

            </div>
        </div>
    </main>

    <?php require "footer.php"; ?>

</body>
</html>
