<?php
$servername = "localhost";
$db_username = "root";
$db_password = "";
$database = "db_coba";

$conn = new mysqli($servername, $db_username, $db_password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
