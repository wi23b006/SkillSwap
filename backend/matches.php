<?php

function get_matches($conn, $user_id) {

    // find all matches of the logged in user
    $sql = "SELECT m.id, p.user_id, p.display_name,
                   p.city, m.created_at
            FROM matches AS m
            JOIN profiles AS p
                ON p.user_id = IF(
                    m.user1_id = ?,
                    m.user2_id,
                    m.user1_id
                )
            WHERE m.user1_id = ?
               OR m.user2_id = ?
            ORDER BY m.created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $user_id, $user_id);
    $stmt->execute();

    // return all matches
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
