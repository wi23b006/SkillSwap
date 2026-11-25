<?php

// EXAMPLE DATA, only till enis got matching backend 

// people with fitting skills (for match.php)
function get_suggestions($conn, $user_id) {
    return [
        [
            "user_id" => 101,
            "display_name" => "Alex",
            "city" => "Vienna",
            "bio" => "I play guitar and want to build my own website.",
            "skills" => [
                ["name" => "Guitar", "skill_type" => "offered"],
                ["name" => "Music Theory", "skill_type" => "offered"],
                ["name" => "HTML & CSS", "skill_type" => "wanted"]
            ]
        ],
        [
            "user_id" => 102,
            "display_name" => "Sam",
            "city" => "Graz",
            "bio" => "Native Spanish speaker, loves good food.",
            "skills" => [
                ["name" => "Spanish Conversation", "skill_type" => "offered"],
                ["name" => "Cooking", "skill_type" => "wanted"]
            ]
        ],
        [
            "user_id" => 103,
            "display_name" => "Robin",
            "city" => "",
            "bio" => "",
            "skills" => [
                ["name" => "Photography", "skill_type" => "offered"]
            ]
        ]
    ];
}

// my matches (for chats.php)
function get_matches($conn, $user_id) {
    return [
        ["id" => 1, "user_id" => 101, "display_name" => "Alex", "city" => "Vienna", "created_at" => "2026-11-23 18:20:00"],
        ["id" => 2, "user_id" => 102, "display_name" => "Sam", "city" => "Graz", "created_at" => "2026-11-20 09:05:00"]
    ];
}

// one match (for chat.php)
function get_match($conn, $user_id, $match_id) {
    foreach (get_matches($conn, $user_id) as $match) {
        if ($match["id"] == $match_id) {
            return $match;
        }
    }

    return null;
}

// messages of one match
function get_messages($conn, $match_id) {

    // no messages yet
    if ($match_id != 1) {
        return [];
    }

    $me = $_SESSION["user_id"];

    return [
        ["sender_id" => 101, "message" => "Hi! I saw that you can teach HTML & CSS.", "created_at" => "2026-11-23 18:25:00"],
        ["sender_id" => $me, "message" => "Yes! And I would love to learn guitar.", "created_at" => "2026-11-23 18:31:00"],
        ["sender_id" => 101, "message" => "Great, do you have time on Thursday?", "created_at" => "2026-11-23 18:32:00"]
    ];
}
