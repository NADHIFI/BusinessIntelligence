//<?php
// Database credentials
$servername = "localhost";
$db_username = "root"; // Ganti dengan username MySQL Anda
$db_password = ""; // Ganti dengan password MySQL Anda
$database = "db_coba"; // Ganti dengan nama database MySQL Anda

// Membuat koneksi
$conn = new mysqli($servername, $db_username, $db_password, $database);

// Memeriksa koneksi
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
    
    // Menghapus tabel lama jika ada dengan nama yang sama
    $conn->query("DROP TABLE IF EXISTS $filename");
    
    // Membuat query untuk membuat tabel baru
    $table = "CREATE TABLE IF NOT EXISTS $filename (";
    foreach($header as $column_name) {
        $column_name = preg_replace('/[^a-zA-Z0-9_]/', '_', $column_name); // Mengganti karakter yang tidak valid untuk nama kolom
        $table .= "`$column_name` VARCHAR(255),"; // Tambahkan tanda kutip pada nama kolom
    }
    $table = rtrim($table, ',');
    $table .= ")";
    
    // Membuat tabel baru
    if ($conn->query($table) === TRUE) {
        // Membaca data dari CSV dan memasukkannya ke dalam database
        while($data = fgetcsv($handle)) {
            // Memeriksa apakah jumlah kolom sesuai dengan jumlah nilai
            if(count($header) == count($data)) {
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
