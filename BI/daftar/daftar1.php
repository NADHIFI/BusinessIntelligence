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

// Retrieve form data and sanitize inputs
$username = $conn->real_escape_string($_POST['username']);
$email = $conn->real_escape_string($_POST['email']);
$password = $conn->real_escape_string($_POST['password']);

// SQL to check if username or email already exists
$sql_check = "SELECT * FROM akun WHERE username = ? OR email = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ss", $username, $email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

// If username or email already exists, display error message
if ($result_check->num_rows > 0) {
    echo "Username atau email sudah terdaftar. Silakan gunakan username atau email lain.";
} else {
    // SQL to insert data into table using prepared statement
    $sql_insert = "INSERT INTO akun (username, email, password) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("sss", $username, $email, $password);

    try {
        // Execute the statement to insert data
        $stmt_insert->execute();
        // If registration is successful, return success message
        echo "Registrasi berhasil!";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    // Close insert statement
    $stmt_insert->close();
}

// Close check statement and connection
$stmt_check->close();
$conn->close();
?>
