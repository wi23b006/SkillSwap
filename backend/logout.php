<?php

session_start();

// user session clearen
$_SESSION = [];

// session löschen
session_destroy();

// zurück zum login
header("Location: ../frontend/login.html");
exit;
