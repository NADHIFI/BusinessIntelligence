<?php
session_start(); // Mulai sesi

$servername = "localhost";
$username = "root";
$password = "";
$database = "db_user";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $database);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Mendapatkan data dari permintaan POST
$email = $_POST['email'];
$name = $_POST['name'];

// Memeriksa apakah pengguna sudah ada di database
$sql_check = "SELECT id, username FROM akun WHERE email = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // Pengguna sudah ada, ambil data pengguna
    $user = $result_check->fetch_assoc();
} else {
    // Pengguna baru, masukkan ke database
    $sql_insert = "INSERT INTO akun (username, email) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ss", $name, $email);
    $stmt_insert->execute();
    // Dapatkan ID pengguna yang baru dimasukkan
    $user_id = $stmt_insert->insert_id;
    // Dapatkan data pengguna yang baru dimasukkan
    $user = [
        'id' => $user_id,
        'username' => $name,
        'email' => $email,
    ];
    $stmt_insert->close();
}

// Setel data pengguna ke dalam sesi
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];

// Menutup koneksi
$stmt_check->close();
$conn->close();

// Mengirim respons sukses
echo 'success';
?>
