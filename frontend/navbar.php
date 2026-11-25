<?php
// navigation for logged in users
// every page sets $page first, so the active link is marked
$nav_links = [
    "match" => ["match.php", "Discover"],
    "chats" => ["chats.php", "My Matches"],
    "profil" => ["profil.php", "My Profile"]
];
?>
    <!-- navigation (logged in) -->
    <nav class="navbar bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand fw-semibold fs-4" href="index.html">SkillSwap</a>
            <div class="d-flex flex-wrap column-gap-4">
                <?php foreach ($nav_links as $nav_page => $nav_link): ?>
                    <?php if ($nav_page == $page): ?>
                        <a class="nav-link fw-semibold border-bottom border-2 border-dark" href="<?= $nav_link[0] ?>" aria-current="page"><?= $nav_link[1] ?></a>
                    <?php else: ?>
                        <a class="nav-link" href="<?= $nav_link[0] ?>"><?= $nav_link[1] ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
                <a class="nav-link" href="../backend/logout.php">Log out</a>
            </div>
        </div>
    </nav>
