
<?php
session_start();
require "../backend/datenbank.php";

// only for logged in users
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html#login-required");
    exit;
}

// backend or example data, till backend is connected
$example = !file_exists("../backend/matching.php");
require $example ? "beispieldaten.php" : "../backend/matching.php";

// active link in the navbar
$page = "match";

// people need fitting skills
$people = [];
$load_error = false;

try {
    $people = get_suggestions($conn, $_SESSION["user_id"]);
} catch (mysqli_sql_exception $error) {
    $load_error = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Discover – SkillSwap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary d-flex flex-column min-vh-100">

    <?php require "navbar.php"; ?>

    <main class="container py-5">

        <h1 class="fw-semibold">Discover skill swaps</h1>
        <p class="text-body-secondary mb-4">Potential matches are based on complementary offered and wanted skills.</p>

        <!-- messages from the backend -->
        <div id="new-match" class="alert alert-success form-error" role="alert">
            It's a match! <a href="chats.php" class="alert-link">Go to my matches</a>
        </div>
        <div id="decision-error" class="alert alert-danger form-error" role="alert">
            That did not work. Please try again.
        </div>

        <!-- the suggestions could not be loaded -->
        <?php if ($load_error): ?>
            <div class="alert alert-danger" role="alert">Your suggestions could not be loaded. Please try again later.</div>
        <?php endif; ?>

        <?php if ($example): ?>
            <div class="alert alert-secondary">TEST data, the matching backend is not connected yet.</div>
        <?php endif; ?>

        <!-- nobody fits -->
        <?php if (count($people) == 0 && !$load_error): ?>
            <div class="card">
                <div class="card-body p-5 text-center">
                    <h2 class="h4">No suggestions right now</h2>
                    <p class="text-body-secondary">Add more skills to your profile to find more people.</p>
                    <a href="profil.php" class="btn btn-dark">Go to my profile</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (count($people) > 0): ?>
            <?php
            // show the first person big and the others under up next
            $person = $people[0];
            $next_people = array_slice($people, 1);
            $name = htmlspecialchars($person["display_name"]);
            ?>

            <div class="row g-4">

                <!-- left: number of suggestions and how it works -->
                <div class="col-lg-3 order-2 order-lg-1">
                    <div class="bg-body-secondary rounded-3 p-3 mb-4">
                        <p class="text-body-secondary small mb-0">Potential matches</p>
                        <p class="h5 mb-0"><?= count($people) ?> <?= count($people) == 1 ? "suggestion" : "suggestions" ?></p>
                    </div>

                    <div class="card">
                        <div class="card-body p-4">
                            <h2 class="h5 mb-3">How it works</h2>
                            <ol class="d-flex flex-column gap-2 ps-3 mb-0">
                                <li>Review the skills</li>
                                <li>Choose Like or Pass</li>
                                <li>Mutual Likes become matches</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- middle: the suggested person -->
                <div class="col-lg-6 order-1 order-lg-2">
                    <div class="card">
                        <div class="card-body p-4 p-md-5">

                            <div class="d-flex align-items-center gap-3 mb-4">

                                <span class="avatar" aria-hidden="true">
                                    <?= htmlspecialchars(mb_substr($person["display_name"], 0, 1)) ?>
                                </span>

                                <div>
                                    <h2 class="h4 mb-0 text-break"><?= $name ?></h2>

                                    <?php if (!empty($person["city"])): ?>
                                        <p class="text-body-secondary mb-0 text-break">
                                            City: <?= htmlspecialchars($person["city"]) ?>
                                        </p>
                                    <?php endif; ?>

                                    <!-- distance between users -->
                                    <?php if (isset($person["distance_km"])): ?>
                                        <p class="text-body-secondary mb-0">

                                            <?php if ($person["distance_km"] == 0): ?>
                                                Same city
                                            <?php else: ?>
                                                Approx. <?= (int)$person["distance_km"] ?> km away (straight line)
                                            <?php endif; ?>

                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if (!empty($person["bio"])): ?>
                                <p class="text-break mb-4">
                                    About: <?= nl2br(htmlspecialchars($person["bio"])) ?>
                                </p>
                            <?php endif; ?>

                            <p class="text-body-secondary mb-2">Skill compatibility</p>

                            <!-- blue = offered -->
                            <div class="bg-body-tertiary rounded-3 p-3 mb-3">
                                <h3 class="h6 text-break">
                                    <span class="dot dot-offered"></span><?= $name ?> offers
                                    <span aria-hidden="true">→</span> You want
                                </h3>

                                <div class="d-flex flex-wrap gap-2">
                                    <?php $count = 0; ?>

                                    <?php foreach ($person["skills"] as $skill): ?>
                                        <?php if ($skill["skill_type"] == "offered"): ?>
                                            <?php $count++; ?>
                                            <span class="chip chip-small chip-offered">
                                                <?= htmlspecialchars($skill["name"]) ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>

                                    <?php if ($count == 0): ?>
                                        <span class="text-body-secondary small">No fitting skill</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- orange = wanted -->
                            <div class="bg-body-tertiary rounded-3 p-3 mb-4">
                                <h3 class="h6 text-break">
                                    <span class="dot dot-wanted"></span>You offer
                                    <span aria-hidden="true">→</span> <?= $name ?> wants
                                </h3>

                                <div class="d-flex flex-wrap gap-2">
                                    <?php $count = 0; ?>

                                    <?php foreach ($person["skills"] as $skill): ?>
                                        <?php if ($skill["skill_type"] == "wanted"): ?>
                                            <?php $count++; ?>
                                            <span class="chip chip-small chip-wanted">
                                                <?= htmlspecialchars($skill["name"]) ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>

                                    <?php if ($count == 0): ?>
                                        <span class="text-body-secondary small">No fitting skill</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- sends like or pass to the backend -->
                            <form action="../backend/entscheidung.php" method="POST" class="d-flex gap-3">

                                <input type="hidden" name="user_id" value="<?= $person["user_id"] ?>">

                                <button type="submit"
                                        name="decision"
                                        value="pass"
                                        class="btn btn-outline-dark w-50 py-3"
                                        aria-label="Pass <?= $name ?>"
                                        <?= $example ? "disabled" : "" ?>>
                                    Pass
                                </button>

                                <button type="submit"
                                        name="decision"
                                        value="like"
                                        class="btn btn-dark w-50 py-3"
                                        aria-label="Like <?= $name ?>"
                                        <?= $example ? "disabled" : "" ?>>
                                    Like
                                </button>
                            </form>

                        </div>
                    </div>
                </div>

                <!-- right: all other suggestions -->
                <div class="col-lg-3 order-3">
                    <div class="card">
                        <div class="card-body p-4">

                            <h2 class="h5 mb-3">Up next</h2>

                            <?php if (count($next_people) == 0): ?>
                                <p class="text-body-secondary mb-0">No more suggestions.</p>
                            <?php endif; ?>

                            <ul class="list-unstyled d-flex flex-column gap-3 mb-0">

                                <?php foreach ($next_people as $next): ?>
                                    <li class="d-flex align-items-center gap-3">

                                        <span class="avatar" aria-hidden="true">
                                            <?= htmlspecialchars(mb_substr($next["display_name"], 0, 1)) ?>
                                        </span>

                                        <span class="text-break">

                                            <span class="d-block fw-semibold">
                                                <?= htmlspecialchars($next["display_name"]) ?>
                                            </span>

                                            <span class="d-block text-body-secondary small">
                                                <?= count($next["skills"]) ?> fitting
                                                <?= count($next["skills"]) == 1 ? "skill" : "skills" ?>
                                            </span>

                                            <!-- distance for up next users -->
                                            <?php if (isset($next["distance_km"])): ?>
                                                <span class="d-block text-body-secondary small">

                                                    <?php if ($next["distance_km"] == 0): ?>
                                                        Same city
                                                    <?php else: ?>
                                                        Approx. <?= (int)$next["distance_km"] ?> km away
                                                    <?php endif; ?>

                                                </span>
                                            <?php endif; ?>

                                        </span>
                                    </li>
                                <?php endforeach; ?>

                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        <?php endif; ?>

    </main>

    <?php require "footer.php"; ?>

</body>
</html>
