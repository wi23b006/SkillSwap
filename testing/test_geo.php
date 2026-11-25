
<?php

require "../backend/datenbank.php";
require "../backend/geo.php";

// get coordinates for both cities
$vienna = get_city_coordinates($conn, "Vienna");
$graz = get_city_coordinates($conn, "Graz");

// calculate distance
if ($vienna && $graz) {

    $distance = calculate_distance($vienna, $graz);

    echo "Vienna to Graz: " . $distance . " km";

} else {
    echo "One of the cities was not found.";
}
