<?php
session_start(); // Mulai sesi

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../login/login1.html"); // Redirect ke halaman login jika belum login
    exit;
}

// Hubungkan ke database
include "../profile/koneksi.php";

// Ambil username dari sesi
$username = $_SESSION['username'];

// Query untuk mengambil data pengguna dari database
$query = "SELECT * FROM akun WHERE username = '$username'";
$result = mysqli_query($koneksi, $query);

// Periksa apakah query berhasil dieksekusi
if ($result) {
    // Ambil baris hasil sebagai array asosiatif
    $row = mysqli_fetch_assoc($result);
    
    // Tampilkan password dalam bentuk teks mentah
    $current_password = $row['Password'];
} else {
    // Jika terjadi kesalahan saat menjalankan query
    $current_password = "Error: Gagal mengambil data dari database";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="profile.css">
    <style>
        /* Navigation bar styling */
        .navbar {
            position: fixed;
            top: 0;
            right: 0;
            height: 100%;
            width: 110px;
            background-color: #333;
            overflow-x: hidden;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .navbar a {
            display: block;
            color: #ddd;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            width: 100%;
            margin: 10px 0; /* Add margin between items */
        }

        .navbar a:hover {
            background-color: #FD8D14;
            color: black;
        }

        .wrapper {
            margin-right: 150px; /* Adjust to avoid overlapping with navbar */
            padding: 20px;
        }

        .navbar .icon {
            display: none;
        }
    </style>
    <!-- CSS styling -->
</head>
<body>
    <div class="profile-container">
        <!-- Konten profil -->
        <div class="profile-pic">
            <!-- Foto profil -->
            <img src="default-profile.png" alt="Foto Profil" id="profile-pic-img">
        </div>
        <div class="profile-details">
            <!-- Form untuk menampilkan data profil -->
            <label for="username">Username</label>
            <!-- Tampilkan username dari database -->
            <input type="text" id="username" name="username" value="<?php echo $_SESSION['username']; ?>" readonly>
            <!-- readonly digunakan agar input tidak bisa diubah oleh pengguna -->

            <label for="email">Email</label>
            <!-- Tampilkan email dari database -->
            <input type="email" id="email" name="email" value="<?php echo $row['Email']; ?>" readonly>
            <!-- readonly digunakan agar input tidak bisa diubah oleh pengguna -->

            <label for="password">Password</label>
            <!-- Tampilkan password dari database dalam bentuk yang disamarkan -->
            <input type="password" id="password" name="password" value="<?php echo str_repeat("*", strlen($current_password)); ?>" readonly>
            <!-- readonly digunakan agar input tidak bisa diubah oleh pengguna -->

            <!-- readonly digunakan agar input tidak bisa diubah oleh pengguna -->
        </div>
        <div class="forgot-password">
            <!-- Tautan untuk lupa password -->
            <a href="reset.html">Lupa Password?</a>
        </div>
        <!-- Tambahkan tombol logout -->
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="navbar">
        <a href="../add-file/index.php">Add File</a>
        <a href="../ViewData/Tampilan.php">Home</a>
        <a href="../visualisasi/visualisasi.php">Visualization</a>
        <a href="../profile/profile.php">Profile</a>
    </div>

    <script>
        // JavaScript untuk mengganti foto profil dan memperbarui profil
        // ...
    </script>
</body>
</html>
