<?php
session_start(); // Mulai sesi

// Periksa apakah pengguna sudah masuk
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    // Jika pengguna belum masuk, arahkan kembali ke halaman login
    header("Location: ../login/login1.html?error=Silakan+login+terlebih+dahulu");
    exit();
}

// Database credentials untuk db_user
$db_servername = "localhost";
$db_username = "root"; // Ganti dengan username MySQL Anda
$db_password = ""; // Ganti dengan password MySQL Anda
$db_name = "db_user"; // Nama database untuk autentikasi pengguna

// Membuat koneksi ke db_user
$conn_user = new mysqli($db_servername, $db_username, $db_password, $db_name);

// Memeriksa koneksi ke db_user
if ($conn_user->connect_error) {
    die("Koneksi gagal ke db_user: " . $conn_user->connect_error);
}

// Periksa sesi di database db_user
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$sql_check_session = "SELECT * FROM akun WHERE ID = ? AND Username = ?";
$stmt = $conn_user->prepare($sql_check_session);
$stmt->bind_param("is", $user_id, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Jika sesi tidak valid, arahkan kembali ke halaman login
    header("Location: ../login/login1.html?error=Sesi+tidak+valid");
    exit();
}

// Tutup koneksi ke db_user
$stmt->close();
$conn_user->close();

// Database credentials untuk data
$servername = "localhost";
$db_username_data = "root"; // Ganti dengan username MySQL Anda
$db_password_data = ""; // Ganti dengan password MySQL Anda
$database = "data"; // Ganti dengan nama database MySQL Anda

// Membuat koneksi ke database data
$conn = new mysqli($servername, $db_username_data, $db_password_data, $database);

// Memeriksa koneksi ke database data
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Jika file CSV diunggah
if(isset($_FILES['file'])) {
    $file = $_FILES['file']['tmp_name'];
    $filename = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
    
    // Mengganti karakter yang tidak valid untuk nama tabel
    $filename = preg_replace('/[^a-zA-Z0-9_]/', '_', $filename);
    
    // Baca file CSV
    $handle = fopen($file, "r");
    
    // Mendapatkan header
    $header = fgetcsv($handle);
    
    // Menambahkan kolom 'uploaded_by'
    array_unshift($header, 'uploaded_by');

    // Menghapus tabel lama jika ada dengan nama yang sama
    $conn->query("DROP TABLE IF EXISTS $filename");
    
    // Membuat query untuk membuat tabel baru
    $table = "CREATE TABLE IF NOT EXISTS $filename (";
    foreach($header as $column_name) {
        $column_name = preg_replace('/[^a-zA-Z0-9_]/', '_', $column_name); // Mengganti karakter yang tidak valid untuk nama kolom
        $table .= "$column_name VARCHAR(255),"; // Tambahkan tanda kutip pada nama kolom
    }
    $table = rtrim($table, ',');
    $table .= ")";
    
    // Membuat tabel baru
    if ($conn->query($table) === TRUE) {
        // Membaca data dari CSV dan memasukkannya ke dalam database
        while($data = fgetcsv($handle)) {
            // Memeriksa apakah jumlah kolom sesuai dengan jumlah nilai
            if(count($header) == count($data) + 1) {
                $data = array_merge([$username], $data);
                $sql = "INSERT INTO $filename VALUES ('" . implode("','", $data) . "')";
                if ($conn->query($sql) !== TRUE) {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                echo "Error: Jumlah kolom tidak sesuai dengan jumlah nilai pada baris CSV.";
            }
        }

        // Menutup handle file
        fclose($handle);

        // Pindahkan file ke direktori yang ditentukan setelah data dimasukkan ke database
        $destination = "C:/xampp/htdocs/Berkas/" . $_FILES['file']['name'];
        move_uploaded_file($file, $destination);

        // Menampilkan pesan popup
        echo "<script>alert('File CSV berhasil diunggah dan data dimasukkan ke dalam database.');</script>";

        // Redirect ke halaman tampilan.php
        echo "<script>window.location.href = '../Viewdata/tampilan.php';</script>";
    } else {
        echo "Error creating table: " . $conn->error;
    }

    // Menutup koneksi
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Upload File</title>
    <link rel="stylesheet" href="style.css">
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
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <a href="index.php">Add File</a>
        <a href="../ViewData/Tampilan.php">Home</a>
        <a href="../visualisasi/visualisasi.php">Visualization</a>
        <a href="../profile/profile.php">Profile</a>
    </div>

    <div class="wrapper">
        <div class="box">
            <div class="input-bx">
                <h2 class="upload-area-title">Upload File</h2>
                <form id="upload-form" action="" method="post" enctype="multipart/form-data">
                    <input type="file" name="file" id="upload" accept=".csv,.xls" hidden>
                    <label for="upload" class="uploadlabel" id="upload-label">
                        <span id="upload-icon"><i class="fa fa-cloud-upload"></i></span>
                        <p id="upload-text">Click To Upload</p> 
                    </label>
                    <div class="submit-wrapper">
                        <button type="submit" name="submit">
                            <img src="../img/tombol.png" alt="Submit" class="submit-image">
                        </button>
                    </div>
                    <div id="loading" class="loading">
                        <i class="fa fa-spinner fa-spin"></i> Uploading...
                    </div>
                </form>
                <div id="complete" class="complete">Complete</div>
            </div>                   
        </div>
    </div>

    <script>
        document.getElementById('upload').addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                document.getElementById('upload-icon').innerHTML = '<i class="fa fa-file"></i>';
                document.getElementById('upload-text').textContent = 'File Selected';
            } else {
                document.getElementById('upload-icon').innerHTML = '<i class="fa fa-cloud-upload"></i>';
                document.getElementById('upload-text').textContent = 'Click To Upload';
            }
        });

        document.getElementById('upload-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting immediately

            // Show the loading message
            document.getElementById('loading').style.display = 'block';
            document.getElementById('complete').style.display = 'none';

            var formData = new FormData(this);

            // Create a new AJAX request
            var xhr = new XMLHttpRequest();
            xhr.open('POST', this.action, true);

            xhr.onload = function() {
                // Hide the loading message
                document.getElementById('loading').style.display = 'none';

                if (xhr.status === 200) {
                    // Show the complete message
                    document.getElementById('complete').style.display = 'block';
                    //Optionally, redirect to another page
                    window.location.href = '../ViewData/Tampilan.php';
                } else {
                    alert('An error occurred while uploading the file.');
                }
            };

            xhr.send(formData);
        });
    </script>
</body>
</html>
