<?php
session_start();
require "../backend/datenbank.php";

// only logged Users can see
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html#login-required");
    exit;
}

// active link in the navbar
$page = "profil";

// load the saved profil (empty if there is none)
$stmt = $conn->prepare("SELECT display_name, bio, city FROM profiles WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc() ?? [];

// load the user skills 
$stmt = $conn->prepare("SELECT skills.id, skills.name, user_skills.skill_type
                        FROM user_skills
                        JOIN skills ON skills.id = user_skills.skill_id
                        WHERE user_skills.user_id = ?
                        ORDER BY skills.name");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$skills = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// remove buttons are disabled till backend is done
$can_remove = file_exists("../backend/skill_entfernen.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My profile - SkillSwap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary d-flex flex-column min-vh-100">

    <?php require "navbar.php"; ?>

    <main class="container py-5">

        <h1 class="fw-semibold">My profile</h1>
        <p class="text-body-secondary mb-4">Set up the skills you can share and the skills you want to learn.</p>

        <div class="row g-4">

            <!-- left: profile information -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3">Profile information</h2>

                        <form action="../backend/profil.php" method="POST">

                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Display name</label>
                                <input type="text" id="name" name="display_name" class="form-control" placeholder="Your name" maxlength="100" required
                                       pattern=".*\S.*" title="Please enter a display name"
                                       value="<?= htmlspecialchars($profile["display_name"] ?? "") ?>">
                            </div>

                            <div class="mb-3">
                                <label for="city" class="form-label fw-semibold">City / region (optional)</label>
                                <input type="text" id="city" name="city" class="form-control" placeholder="City / region" maxlength="100"
                                       value="<?= htmlspecialchars($profile["city"] ?? "") ?>">
                            </div>

                            <div class="mb-4">
                                <label for="bio" class="form-label fw-semibold">Bio</label>
                                <textarea id="bio" name="bio" class="form-control" rows="3" placeholder="Tell others what you can share or learn"><?= htmlspecialchars($profile["bio"] ?? "") ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-dark w-100 py-2">Save profile</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- right: skills -->
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-body p-4">

                        <!-- error message -->
                        <div id="skill-error" class="alert alert-danger form-error" role="alert">
                            The skill was not removed. Please try again!
                        </div>

                        <?php if (!$can_remove && count($skills) > 0): ?>
                            <div class="alert alert-secondary">Removing skills is not connected to the backend yet.</div>
                        <?php endif; ?>

                        <!-- blue = offered -->
                        <h2 class="h4"><span class="dot dot-offered"></span>Skills I can offer</h2>
                        <p class="text-body-secondary small">Choose from existing skills in the catalogue</p>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($skills as $skill): ?>
                                <?php if ($skill["skill_type"] == "offered"): ?>
                                    <form action="../backend/skill_entfernen.php" method="POST" class="chip chip-offered chip-form">
                                        <input type="hidden" name="skill_id" value="<?= $skill["id"] ?>">
                                        <input type="hidden" name="skill_type" value="offered">
                                        <?= htmlspecialchars($skill["name"]) ?>
                                        <button type="submit" class="chip-remove" <?= $can_remove ? "" : "disabled" ?>
                                                aria-label="Remove <?= htmlspecialchars($skill["name"]) ?> from offered skills"><span aria-hidden="true">&times;</span></button>
                                    </form>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <a href="skills.php?type=offered" class="btn btn-offered rounded-pill">+ Add offered skill</a>
                        </div>

                        <hr class="my-4">

                        <!-- orange = wanted -->
                        <h2 class="h4"><span class="dot dot-wanted"></span>Skills I want to learn</h2>
                        <p class="text-body-secondary small">Pick skills you would like to receive in return</p>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($skills as $skill): ?>
                                <?php if ($skill["skill_type"] == "wanted"): ?>
                                    <form action="../backend/skill_entfernen.php" method="POST" class="chip chip-wanted chip-form">
                                        <input type="hidden" name="skill_id" value="<?= $skill["id"] ?>">
                                        <input type="hidden" name="skill_type" value="wanted">
                                        <?= htmlspecialchars($skill["name"]) ?>
                                        <button type="submit" class="chip-remove" <?= $can_remove ? "" : "disabled" ?>
                                                aria-label="Remove <?= htmlspecialchars($skill["name"]) ?> from wanted skills"><span aria-hidden="true">&times;</span></button>
                                    </form>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <a href="skills.php?type=wanted" class="btn btn-wanted rounded-pill">+ Add wanted skill</a>
                        </div>

                        <div class="bg-body-tertiary rounded p-3 mt-5">
                            <p class="fw-semibold mb-1">Privacy note</p>
                            <p class="text-body-secondary small mb-0">You talk to your matches in the chat. Nobody else can see this. </p>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php require "footer.php"; ?>

</body>
</html>
