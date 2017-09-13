<?php

DEFINE ('DB_USER', 'user');
DEFINE ('DB_PASSWORD', 'password');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'Chatdb');
DEFINE ('DB_PORT', '7777');

$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT)
OR die('Could not connect to DB: ' . mysqli_connect_error());

?>
