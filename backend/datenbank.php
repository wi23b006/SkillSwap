<?php

$server = "localhost";
$user = "root";
$password = "";
$database = "skillswap";

$conn = new mysqli($server, $user, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed");
}
