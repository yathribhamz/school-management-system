<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sms";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed.");
}

if (!$conn->set_charset("utf8mb4")) {
    die("Connection failed.");
}
?>
