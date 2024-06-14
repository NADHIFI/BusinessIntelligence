<?php
require 'config.php';

// Membuat koneksi
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

function sanitize($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Jika file CSV diunggah
if (isset($_FILES['file'])) {
    $file = $_FILES['file']['tmp_name'];
    $filename = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
    
    // Mengganti karakter yang tidak valid untuk nama tabel
    $filename = preg_replace('/[^a-zA-Z0-9_]/', '_', $filename);
    
    // Validasi jenis file
    $file_type = mime_content_type($file);
    if ($file_type !== 'text/csv') {
        die("Format file tidak didukung. Harap unggah file CSV.");
    }
    
    // Baca file CSV
    $handle = fopen($file, "r");
    
    // Mendapatkan header
    $header = fgetcsv($handle);
    
    // Menghapus tabel lama jika ada dengan nama yang sama
    $conn->query("DROP TABLE IF EXISTS `$filename`");
    
    // Membuat query untuk membuat tabel baru
    $table = "CREATE TABLE IF NOT EXISTS `$filename` (";
    foreach ($header as $column_name) {
        $column_name = preg_replace('/[^a-zA-Z0-9_]/', '_', sanitize($column_name)); // Mengganti karakter yang tidak valid untuk nama kolom
        $table .= "`$column_name` VARCHAR(255),";
    }
    $table = rtrim($table, ',');
    $table .= ")";
    
    // Membuat tabel baru
    if ($conn->query($table) === TRUE) {
        // Membaca data dari CSV dan memasukkannya ke dalam database
        while ($data = fgetcsv($handle)) {
            // Memeriksa apakah jumlah kolom sesuai dengan jumlah nilai
            if (count($header) == count($data)) {
                $data = array_map('sanitize', $data); // Sanitasi data
                $sql = "INSERT INTO `$filename` VALUES ('" . implode("','", $data) . "')";
                $conn->query($sql);
            } else {
                echo "Error: Jumlah kolom tidak sesuai dengan jumlah nilai pada baris CSV.";
            }
        }

        // Menutup handle file
        fclose($handle);

        // Pindahkan file ke direktori yang ditentukan
        $destination = "C:/xampp/htdocs/Berkas/" . sanitize($_FILES['file']['name']);
        move_uploaded_file($file, $destination);

        // Menampilkan pesan popup dan meta refresh
        echo '<html><head>';
        echo '<script>alert("File CSV berhasil diunggah dan data dimasukkan ke dalam database.");</script>';
        echo '<meta http-equiv="refresh" content="3;url=http://localhost/BI/ViewData/Tampilan.php">';
        echo '</head><body>';
        echo '<p>Redirecting to ViewData in 3 seconds...</p>';
        echo '</body></html>';
    } else {
        echo "Error creating table: " . $conn->error;
    }
}
?>
