<?php
// Mendefinisikan koneksi ke database
$servername = "localhost"; // Ganti dengan nama server Anda
$username = "root"; // Ganti dengan nama pengguna MySQL Anda
$password = ""; // Ganti dengan kata sandi MySQL Anda
$database = "db_user"; // Ganti dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $database);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil nilai email dari permintaan GET
$email = $_GET['email'];

// Lakukan query ke database untuk memeriksa apakah email ada di database
$sql = "SELECT * FROM akun WHERE email = '$email'";
$result = $conn->query($sql);

// Buat objek untuk menyimpan hasil verifikasi
$response = new stdClass();

if ($result->num_rows > 0) {
    // Email ditemukan dalam database
    $response->exists = true;
} else {
    // Email tidak ditemukan dalam database
    $response->exists = false;
}

// Mengembalikan respons dalam format JSON
header('Content-Type: application/json');
echo json_encode($response);

// Tutup koneksi ke database
$conn->close();
?>
