<?php

session_start();

// user session clearen
$_SESSION = [];

// session löschen
session_destroy();

echo "Logout successful!";
