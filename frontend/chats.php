<?php

session_start();
require "../backend/datenbank.php";

// only for logged in users
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html#login-required");
    exit;
}

// load real matches from the database
require "../backend/matches.php";

$page = "chats";

$matches = get_matches($conn, $_SESSION["user_id"]);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My matches - SkillSwap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary d-flex flex-column min-vh-100">

    <?php require "navbar.php"; ?>

    <main class="container py-5">
        <div class="col-lg-8 mx-auto">

            <h1 class="fw-semibold">My matches</h1>
            <p class="text-body-secondary mb-4">
                Your mutual matches. Chat will be available soon.
            </p>

            <div class="card overflow-hidden">

                <!-- no matches yet -->
                <?php if (count($matches) == 0): ?>
                    <div class="card-body p-5 text-center">
                        <h2 class="h4">No matches yet</h2>
                        <p class="text-body-secondary">
                            Like someone. If they like you too, they will appear here.
                        </p>
                        <a href="match.php" class="btn btn-dark">
                            Discover people
                        </a>
                    </div>
                <?php endif; ?>

                <!-- show real matches -->
                <?php foreach ($matches as $match): ?>

                    <div class="p-4 border-bottom d-flex align-items-center gap-3">

                        <span class="avatar" aria-hidden="true">
                            <?= htmlspecialchars(mb_substr($match["display_name"], 0, 1)) ?>
                        </span>

                        <span class="flex-grow-1 text-break">

                            <span class="d-block fw-semibold">
                                <?= htmlspecialchars($match["display_name"]) ?>
                            </span>

                            <span class="d-block text-body-secondary small">
                                Matched on <?= date("d M Y", strtotime($match["created_at"])) ?>
                            </span>

                        </span>

                        <span class="badge text-bg-light border">
                            Chat coming soon
                        </span>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>
    </main>

    <?php require "footer.php"; ?>

</body>
</html>
