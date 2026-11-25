<?php
session_start();
require "../backend/datenbank.php";

// only for logged in users
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html#login-required");
    exit;
}
// backend or example data, till backend is connected 
$example = !file_exists("../backend/chat.php");
require $example ? "beispieldaten.php" : "../backend/chat.php";

// active link in the navbar
$page = "chats";

// which match? (from the link)
$match_id = (int) ($_GET["match"] ?? 0);
$match = get_match($conn, $_SESSION["user_id"], $match_id);

// users can only open their own matches
if (!$match) {
    header("Location: chats.php#chat-error");
    exit;
}

$name = htmlspecialchars($match["display_name"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>It's a match! - SkillSwap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary d-flex flex-column min-vh-100">

    <?php require "navbar.php"; ?>

    <main class="container py-5">
        <div class="col-lg-6 mx-auto">

            <?php if ($example): ?>
                <div class="alert alert-secondary">This is TEST data, the chat backend is still empty.</div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body p-4 p-md-5 text-center">

                    <!-- both people -->
                    <div class="d-flex justify-content-center gap-3 mb-4" aria-hidden="true">
                        <span class="avatar">You</span>
                        <span class="avatar"><?= htmlspecialchars(mb_substr($match["display_name"], 0, 1)) ?></span>
                    </div>

                    <h1 class="fw-semibold"><span aria-hidden="true">✓</span> It's a match!</h1>
                    <p class="lead text-break mb-1">You and <?= $name ?> have both liked each other.</p>

                    <?php if (!empty($match["city"])): ?>
                        <p class="text-body-secondary text-break mb-0">City: <?= htmlspecialchars($match["city"]) ?></p>
                    <?php endif; ?>

                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 mt-5">
                        <a href="match.php" class="btn btn-outline-dark px-4 py-3">Keep discovering</a>
                        <a href="chat.php?match=<?= $match_id ?>" class="btn btn-dark px-4 py-3">Start chatting</a>
                    </div>

                </div>
            </div>

        </div>
    </main>

    <?php require "footer.php"; ?>

</body>
</html>
