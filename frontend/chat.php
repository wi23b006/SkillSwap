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

// which match?
$match_id = (int) ($_GET["match"] ?? 0);
$match = get_match($conn, $_SESSION["user_id"], $match_id);

// users only open their own chats!
if (!$match) {
    header("Location: chats.php#chat-error");
    exit;
}

$name = htmlspecialchars($match["display_name"]);
$messages = get_messages($conn, $match_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chat with <?= $name ?> - SkillSwap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary d-flex flex-column min-vh-100">

    <?php require "navbar.php"; ?>

    <main class="container py-5">
        <div class="col-lg-8 mx-auto">

            <p><a href="chats.php">Back to my matches</a></p>

            <?php if ($example): ?>
                <div class="alert alert-secondary">Example data, the chat backend is not connected yet.</div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body p-4">

                    <h1 class="h4 d-flex align-items-center gap-3 text-break mb-0">
                        <span class="avatar" aria-hidden="true"><?= htmlspecialchars(mb_substr($match["display_name"], 0, 1)) ?></span>
                        Chat with <?= $name ?>
                    </h1>

                    <hr class="my-4">

                    <!-- no messages -->
                    <?php if (count($messages) == 0): ?>
                        <p class="text-body-secondary text-center py-4">No messages yet. Say hello!</p>
                    <?php endif; ?>

                    <!-- all messages, oldest first -->
                    <ol class="list-unstyled d-flex flex-column gap-3 mb-4">
                        <?php foreach ($messages as $message): ?>
                            <?php $own = $message["sender_id"] == $_SESSION["user_id"]; ?>
                            <li class="message <?= $own ? "message-own" : "" ?>">
                                <span class="d-block small fw-semibold"><?= $own ? "You" : $name ?>, <?= date("d M, H:i", strtotime($message["created_at"])) ?></span>
                                <?= nl2br(htmlspecialchars($message["message"])) ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>

                    <!-- error message -->
                    <div id="message-error" class="alert alert-danger form-error" role="alert">
                        Your message was not sent. Please try again!
                    </div>

                    <!-- sends the message to the backend -->
                    <form id="write" action="../backend/nachricht.php" method="POST">

                        <input type="hidden" name="match_id" value="<?= $match_id ?>">

                        <label for="message" class="form-label fw-semibold">Your message</label>
                        <textarea id="message" name="message" class="form-control mb-3" rows="3" maxlength="1000" required></textarea>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="chat.php?match=<?= $match_id ?>">Reload messages</a>
                            <button type="submit" class="btn btn-dark px-4 py-2" <?= $example ? "disabled" : "" ?>>Send</button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </main>

    <?php require "footer.php"; ?>

</body>
</html>
