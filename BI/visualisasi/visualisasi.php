<?php
session_start();
$servername = "localhost";
$db_username = "root";
$db_password = "";
$database = "db_coba";

$conn = new mysqli($servername, $db_username, $db_password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$selected_table = isset($_SESSION['selected_table']) ? $_SESSION['selected_table'] : null;
$selected_columns = isset($_SESSION['selected_columns']) ? $_SESSION['selected_columns'] : [];

if ($selected_table && !empty($selected_columns)) {
    $columns = implode(",", array_map(function($col) { return "`$col`"; }, $selected_columns));
    $result = $conn->query("SELECT $columns FROM `$selected_table`");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Menulis data ke file JSON
    $json_data = json_encode($data);
    file_put_contents('data.json', $json_data);

    // Memanggil Jupyter Notebook untuk menghasilkan visualisasi
    $output = shell_exec('jupyter nbconvert --to notebook --execute "C:\\xampp\\htdocs\\BI\\visualisasi\\visualize.ipynb" --stdout 2>&1');

    if (strpos($output, 'Traceback') !== false) {
        echo "Error: " . $output;
    }
}

// Redirect ke halaman visualisasi
header("Location: visualisasi.html");
exit;
?>
