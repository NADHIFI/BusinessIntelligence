<?php
session_start(); // Mulai sesi

$servername = "localhost";
$username = "root";
$password = "";
$database = "db_user";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['Username'];
    $password = $_POST['Password'];

    // Verifikasi kredensial pengguna menggunakan prepared statements
    $stmt = $conn->prepare("SELECT ID, username FROM akun WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Jika pengguna ada, ambil ID dan username
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['ID']; // Simpan ID pengguna dalam sesi
        $_SESSION['username'] = $row['username']; // Simpan nama pengguna dalam sesi

        // Arahkan ke halaman add-file.php
        header("Location: ../add-file/index.php");
        exit(); // Pastikan tidak ada kode tambahan yang dijalankan setelah ini
    } else {
        // Feedback kepada pengguna jika login gagal
        header("Location: login1.html?error=Login+gagal"); // Arahkan kembali ke halaman login dengan pesan error
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
