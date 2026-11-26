
<?php

session_start();

require "../backend/datenbank.php";
require "../backend/matches.php";
require "../backend/cometchat.php";
require "../backend/cometchat_config.php";

// only logged in users can open the chat
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html#login-required");
    exit;
}

// get the match id from the URL
$match_id = (int) ($_GET["match"] ?? 0);

// check if this match belongs to the logged in user
$match = get_match($conn, $_SESSION["user_id"], $match_id);

// deny access if there is no matching relationship
if (!$match) {
    header("Location: chats.php#chat-error");
    exit;
}

// get the matched user's CometChat ID
$other_user_id = (int) $match["user_id"];
$other_cometchat_uid = "skillswap_" . $other_user_id;

// create an auth token for the logged in user
$auth_token = create_cometchat_auth_token(
    $_SESSION["user_id"]
);

$page = "chats";

$name = htmlspecialchars(
    $match["display_name"],
    ENT_QUOTES,
    "UTF-8"
);

// prevent caching of pages containing auth tokens
header("Cache-Control: no-store, private");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Chat with <?= $name ?> - SkillSwap</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <link href="css/style.css" rel="stylesheet">

    <!-- CometChat Widget v2 -->
    <script
        defer
        src="https://cdn.jsdelivr.net/npm/@cometchat/chat-embed@2.x.x/dist/main.js"
    ></script>

</head>

<body class="bg-body-tertiary d-flex flex-column min-vh-100">

    <?php require "navbar.php"; ?>

    <main class="container py-5">

        <div class="col-lg-8 mx-auto">

            <p>
                <a href="chats.php">
                    Back to my matches
                </a>
            </p>

            <div class="card">

                <div class="card-body p-4">

                    <h1 class="h4 d-flex align-items-center gap-3 mb-4">

                        <span class="avatar" aria-hidden="true">
                            <?= htmlspecialchars(
                                mb_substr(
                                    $match["display_name"],
                                    0,
                                    1
                                ),
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>
                        </span>

                        Chat with <?= $name ?>

                    </h1>

                    <hr>

                    <?php if ($auth_token): ?>

                        <!-- CometChat appears here -->
                        <div id="cometChatMount"></div>

                    <?php else: ?>

                        <div class="alert alert-danger">
                            Chat could not be loaded.
                            Please try again later.
                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </main>

    <?php require "footer.php"; ?>

    <?php if ($auth_token): ?>

    <script type="module">

        const COMETCHAT_WIDGET_CONFIG = {

            // CometChat application
            appID: <?= json_encode($cometchat_app_id) ?>,
            region: <?= json_encode($cometchat_region) ?>,

            // secure login using our PHP backend
            mode: "authToken",

            authToken: <?= json_encode(
                $auth_token,
                JSON_HEX_TAG |
                JSON_HEX_AMP |
                JSON_HEX_APOS |
                JSON_HEX_QUOT
            ) ?>,

            // widget language
            language: "en-US",

            // embedded chat layout
            mount: "#cometChatMount",
            width: "100%",
            height: "650px",
            isDocked: false,

            // open the matched user's conversation
            chatType: "user",

            defaultChatID: <?= json_encode(
                $other_cometchat_uid
            ) ?>,

            // do not automatically open another conversation
            autoOpenFirstItem: false
        };

        // initialize CometChat
        window.addEventListener("load", () => {

            if (
                typeof CometChatApp === "undefined" ||
                !CometChatApp.CometChatAuth
            ) {
                document.getElementById(
                    "cometChatMount"
                ).textContent = "CometChat could not be loaded.";

                return;
            }

            CometChatApp.CometChatAuth.start(
                COMETCHAT_WIDGET_CONFIG
            );

        });

    </script>

    <?php endif; ?>

</body>
</html>
