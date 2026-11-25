
<?php

function get_city_coordinates($conn, $city) {

    $city = trim($city);

    if ($city == "") {
        return null;
    }

    // check if the city is already in our database
    $stmt = $conn->prepare(
        "SELECT latitude, longitude
         FROM city_coordinates
         WHERE city = ?"
    );

    $stmt->bind_param("s", $city);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_assoc();

    // return saved coordinates if found
    if ($result) {
        return $result;
    }

    // search for the city using OpenStreetMap
    $url = "https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q="
           . urlencode($city);

    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt(
        $curl,
        CURLOPT_USERAGENT,
        "SkillSwapStudentProject/1.0 (https://github.com/wi23b006/SkillSwap)"
    );

    // limit requests to one per second
    $lock = fopen(
        sys_get_temp_dir() . "/skillswap_nominatim.lock",
        "c+"
    );

    if (!$lock || !flock($lock, LOCK_EX)) {
        if ($lock) {
            fclose($lock);
        }

        curl_close($curl);
        return null;
    }

    // read the time of the last request
    rewind($lock);
    $last_request = (float) stream_get_contents($lock);

    $wait = 1.1 - (microtime(true) - $last_request);

    // wait if the last request was too recent
    if ($wait > 0) {
        usleep((int) ($wait * 1000000));
    }

    // save the current request time
    rewind($lock);
    ftruncate($lock, 0);
    fwrite($lock, (string) microtime(true));
    fflush($lock);

    // send the request
    $response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    // release the lock
    flock($lock, LOCK_UN);
    fclose($lock);

    if ($response === false || $status != 200) {
        return null;
    }

    $data = json_decode($response, true);

    // stop if the city was not found
    if (empty($data)) {
        return null;
    }

    $latitude = (float) $data[0]["lat"];
    $longitude = (float) $data[0]["lon"];

    // save the coordinates in our database
    $stmt = $conn->prepare(
        "INSERT IGNORE INTO city_coordinates
         (city, latitude, longitude)
         VALUES (?, ?, ?)"
    );

    $stmt->bind_param("sdd", $city, $latitude, $longitude);
    $stmt->execute();

    return [
        "latitude" => $latitude,
        "longitude" => $longitude
    ];
}


// calculate distance between two cities
function calculate_distance($city1, $city2) {

    // convert coordinates to radians
    $lat1 = deg2rad($city1["latitude"]);
    $lon1 = deg2rad($city1["longitude"]);

    $lat2 = deg2rad($city2["latitude"]);
    $lon2 = deg2rad($city2["longitude"]);

    // calculate distance between two locations
    $a = sin(($lat2 - $lat1) / 2) ** 2
       + cos($lat1) * cos($lat2)
       * sin(($lon2 - $lon1) / 2) ** 2;

    $distance = 6371 * 2 * asin(min(1, sqrt($a)));

    // return distance in kilometers
    return round($distance);
}
