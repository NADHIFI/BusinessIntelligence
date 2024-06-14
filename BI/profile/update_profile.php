<?php
session_start(); // Mulai sesi

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../login/login1.php"); // Redirect ke halaman login jika belum login
    exit;
}

// Hubungkan ke database
include "../koneksi.php";

// Ambil username dari sesi
$username = $_SESSION['username'];

// Ambil data yang dikirim dari frontend
$newUsername = $_POST['newUsername'];
$newEmail = $_POST['newEmail'];
$newPassword = $_POST['newPassword'];

// Query untuk memperbarui data pengguna di database
$query = "UPDATE akun SET Username = '$newUsername', Email = '$newEmail', Password = '$newPassword' WHERE Username = '$username'";
$result = mysqli_query($koneksi, $query);

// Periksa apakah query berhasil dieksekusi
if ($result) {
    echo "Data berhasil diperbarui"; // Kirim respons ke frontend
} else {
    echo "Gagal memperbarui data"; // Kirim respons ke frontend
}

// Tutup koneksi ke database
mysqli_close($koneksi);
?>
