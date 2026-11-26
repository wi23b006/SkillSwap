
<?php

function create_cometchat_user($conn, $user_id) {

    // load CometChat settings
    require __DIR__ . "/cometchat_config.php";

    // get the user's name from our database
    $stmt = $conn->prepare(
        "SELECT display_name FROM profiles WHERE user_id = ?"
    );

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        return false;
    }

    // create a unique CometChat user id
    $uid = "skillswap_" . $user_id;

    // CometChat API address
    $url = "https://" . $cometchat_app_id .
           ".api-" . $cometchat_region .
           ".cometchat.io/v3/users";

    // user data sent to CometChat
    $data = [
        "uid" => $uid,
        "name" => $user["display_name"]
    ];

    // send the request
    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_TIMEOUT, 15);

    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "apikey: " . $cometchat_api_key
    ]);

    $response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    // check if the user was created
    if ($response !== false && $status >= 200 && $status < 300) {
        return true;
    }

    return false;
}


// create a login token for an existing CometChat user
function create_cometchat_auth_token($user_id) {

    // load CometChat settings
    require __DIR__ . "/cometchat_config.php";

    // get the user's CometChat id
    $uid = "skillswap_" . (int)$user_id;

    // CometChat API address
    $url = "https://" . $cometchat_app_id .
           ".api-" . $cometchat_region .
           ".cometchat.io/v3/users/" . $uid . "/auth_tokens";

    // send the request
    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 15);

    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
        "force" => true
    ]));

    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "apikey: " . $cometchat_api_key
    ]);

    $response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    // check if the token was created
    if ($response === false || $status != 200) {
        return null;
    }

    $data = json_decode($response, true);

    // return the login token
    return $data["data"]["authToken"] ?? null;
}


// connect two users as friends after a match
function sync_cometchat_match($conn, $match_id) {

    // load CometChat settings
    require __DIR__ . "/cometchat_config.php";

    // get the two users from our matches table
    $stmt = $conn->prepare(
        "SELECT user1_id, user2_id
         FROM matches
         WHERE id = ?"
    );

    $stmt->bind_param("i", $match_id);
    $stmt->execute();

    $match = $stmt->get_result()->fetch_assoc();

    if (!$match) {
        return false;
    }

    // get CometChat user ids
    $user1 = "skillswap_" . $match["user1_id"];
    $user2 = "skillswap_" . $match["user2_id"];

    // connect both users as friends
    $pairs = [
        [$user1, $user2],
        [$user2, $user1]
    ];

    foreach ($pairs as $pair) {

        // CometChat Friends API
        $url = "https://" . $cometchat_app_id .
               ".api-" . $cometchat_region .
               ".cometchat.io/v3/users/" . $pair[0] . "/friends";

        $data = [
            "accepted" => [$pair[1]]
        ];

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "apikey: " . $cometchat_api_key
        ]);

        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        // stop if adding a friend failed
        if ($response === false || $status < 200 || $status >= 300) {
            return false;
        }
    }

    return true;
}
