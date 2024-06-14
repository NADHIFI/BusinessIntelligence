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

// Memeriksa apakah ada pengiriman data menggunakan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai username, email, dan password baru dari formulir
    $username = $_POST['username'];
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];

    // Lakukan query untuk memperbarui password di database
    $sql = "UPDATE akun SET password = '$new_password' WHERE username = '$username' AND email = '$email'";
    if ($conn->query($sql) === TRUE) {
        // Setelah password berhasil diperbarui, redirect ke halaman login.html dengan pesan query string
        header("Location: ../login/login1.html?pesan=password_diperbarui");
        exit(); // Penting untuk menggunakan exit setelah header() untuk mencegah eksekusi lebih lanjut dari skrip PHP
    } else {
        echo "Error updating password: " . $conn->error;
    }
}

// Tutup koneksi ke database
$conn->close();
?>
