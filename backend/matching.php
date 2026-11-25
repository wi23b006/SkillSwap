
<?php

require_once __DIR__ . "/geo.php";

function get_suggestions($conn, $user_id) {

    // find users with matching skills
    $sql = "SELECT p.user_id, p.display_name, p.city, p.bio,
                   s.name AS skill_name, other.skill_type
            FROM user_skills AS mine
            JOIN user_skills AS other
                ON mine.skill_id = other.skill_id
                AND mine.skill_type != other.skill_type
            JOIN profiles AS p ON p.user_id = other.user_id
            JOIN skills AS s ON s.id = other.skill_id
            WHERE mine.user_id = ?
                AND other.user_id != ?
                AND p.moderation_status = 'visible'
                AND other.user_id NOT IN (
                    SELECT to_user_id FROM decisions WHERE from_user_id = ?
                )
            ORDER BY p.display_name, s.name";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $people = [];

    while ($row = $result->fetch_assoc()) {

        $id = $row["user_id"];

        // add each user only once
        if (!isset($people[$id])) {
            $people[$id] = [
                "user_id" => $id,
                "display_name" => $row["display_name"],
                "city" => $row["city"],
                "bio" => $row["bio"],
                "skills" => []
            ];
        }

        // add matching skills
        $people[$id]["skills"][] = [
            "name" => $row["skill_name"],
            "skill_type" => $row["skill_type"]
        ];
    }

    $people = array_values($people);

    // get my city
    $stmt = $conn->prepare(
        "SELECT city FROM profiles WHERE user_id = ?"
    );

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $my_city = $stmt->get_result()->fetch_assoc()["city"] ?? "";

    // calculate distance to each user
    $my_coordinates = get_city_coordinates($conn, $my_city);

    foreach ($people as $i => $person) {

        $other_coordinates = get_city_coordinates(
            $conn,
            $person["city"] ?? ""
        );

        if ($my_coordinates && $other_coordinates) {

            $people[$i]["distance_km"] = calculate_distance(
                $my_coordinates,
                $other_coordinates
            );

        } else {
            $people[$i]["distance_km"] = null;
        }
    }

    // check if both users can teach each other
    foreach ($people as $i => $person) {

        $can_teach = false;
        $can_learn = false;

        foreach ($person["skills"] as $skill) {

            if ($skill["skill_type"] == "offered") {
                $can_teach = true;
            }

            if ($skill["skill_type"] == "wanted") {
                $can_learn = true;
            }
        }

        $people[$i]["both_ways"] = $can_teach && $can_learn;
    }

    // sort matching users
    usort($people, function($a, $b) use ($my_city) {

        // mutual skill swaps come first
        if ($a["both_ways"] != $b["both_ways"]) {
            return (int)$b["both_ways"] - (int)$a["both_ways"];
        }

        // more matching skills come next
        if (count($a["skills"]) != count($b["skills"])) {
            return count($b["skills"]) - count($a["skills"]);
        }

        // users from the same city come next
        $a_same_city = $my_city != "" &&
            strcasecmp($a["city"] ?? "", $my_city) == 0;

        $b_same_city = $my_city != "" &&
            strcasecmp($b["city"] ?? "", $my_city) == 0;

        if ($a_same_city != $b_same_city) {
            return (int)$b_same_city - (int)$a_same_city;
        }

        // alphabetical order if everything is equal
        return strcasecmp($a["display_name"], $b["display_name"]);
    });

    return $people;
}
