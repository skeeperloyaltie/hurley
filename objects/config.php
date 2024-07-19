<?php
// Database configuration settings
define('DB_SERVER', 'localhost');   // Database server
define('DB_USERNAME', 'root');      // Database username
define('DB_PASSWORD', '1391');          // Database password
define('DB_NAME', 'hurley');   // Database name

// Attempt to connect to MySQL database using MySQLi
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($mysqli->connect_error) {
    die('ERROR: Could not connect. ' . $mysqli->connect_error);
}

// Set charset to utf8 for internationalization and improved security
$mysqli->set_charset("utf8");

// Function to close the database connection
function closeConnection($mysqli) {
    $mysqli->close();
}
?>
