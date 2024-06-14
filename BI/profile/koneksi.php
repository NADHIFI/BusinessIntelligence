<?php
$host = "localhost"; // Host database
$username = "root"; // Username database
$password = ""; // Password database
$database = "db_user"; // Nama database

// Membuat koneksi ke database
$koneksi = mysqli_connect($host, $username, $password, $database);

// Periksa koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
