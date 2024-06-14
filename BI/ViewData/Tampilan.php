<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data CSV</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
        .pagination a {
            margin: 0 5px;
            padding: 10px 15px;
            text-decoration: none;
            color: #007bff;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .pagination a.active {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }
        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
    </style>
</head>
<body>
<button onclick="history.go(-1);">Back</button>

<div class="container">
    <h2>Data dari CSV</h2>

    <form method="POST">
        <label for="table">Pilih Tabel:</label>
        <select name="table" onchange="this.form.submit()">
            <?php
            $servername = "localhost";
            $db_username = "root";
            $db_password = "";
            $database = "db_coba";

            $conn = new mysqli($servername, $db_username, $db_password, $database);
            if ($conn->connect_error) {
                die("Koneksi gagal: " . $conn->connect_error);
            }
            $conn->set_charset("utf8mb4");

            $result = $conn->query("SHOW TABLES");
            while($row = $result->fetch_assoc()) {
                $selected = (isset($_POST['table']) && $_POST['table'] == $row['Tables_in_db_coba']) ? 'selected' : '';
                echo "<option value='" . $row['Tables_in_db_coba'] . "' $selected>" . $row['Tables_in_db_coba'] . "</option>";
            }
            ?>
        </select>
    </form>

    <form method="POST" action="">
        <?php
        if (isset($_POST['table'])) {
            $_SESSION['selected_table'] = $_POST['table'];
            unset($_SESSION['selected_columns']);
            unset($_GET['page']);
        }
        $selected_table = isset($_SESSION['selected_table']) ? $_SESSION['selected_table'] : null;
        
        if ($selected_table) {
            echo "<input type='hidden' name='table' value='$selected_table'>";
            try {
                $result = $conn->query("SHOW COLUMNS FROM `$selected_table`");
                echo "<div>";
                while($row = $result->fetch_assoc()) {
                    $checked = (isset($_SESSION['selected_columns']) && in_array($row['Field'], $_SESSION['selected_columns'])) ? 'checked' : '';
                    echo "<label><input type='checkbox' name='selected_columns[]' value='" . $row['Field'] . "' $checked>" . $row['Field'] . "</label>";
                }
                echo "</div>";
            } catch (mysqli_sql_exception $e) {
                echo "Error: " . $e->getMessage();
            }
        }

        if (isset($_POST['selected_columns'])) {
            $_SESSION['selected_columns'] = $_POST['selected_columns'];
        }
        ?>
        <button type="submit" name="filter">Filter</button>
        <button type="submit" name="visualize" formaction="../visualisasi/visualisasi.php">Visualisasi</button>
    </form>


    <div class="table-container">
        <?php
        if (!$selected_table) {
            echo "<p>Pilih tabel untuk melanjutkan.</p>";
        } elseif (!isset($_SESSION['selected_columns']) || empty($_SESSION['selected_columns'])) {
            echo "<p>Pilih setidaknya satu kolom untuk ditampilkan.</p>";
        } else {
            ?>
            <table>
                <thead>
                    <tr>
                        <?php
                        foreach ($_SESSION['selected_columns'] as $column) {
                            echo "<th>" . $column . "</th>";
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $selected_columns = $_SESSION['selected_columns'];
                    $columns = implode(",", array_map(function($col) { return "`$col`"; }, $selected_columns));
                    $page = isset($_GET['page']) ? $_GET['page'] : 1;
                    $start = ($page - 1) * 50;

                    try {
                        $result = $conn->query("SELECT $columns FROM `$selected_table` LIMIT $start, 50");

                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            foreach ($selected_columns as $column) {
                                echo "<td>" . $row[$column] . "</td>";
                            }
                            echo "</tr>";
                        }
                    } catch (mysqli_sql_exception $e) {
                        echo "Error: " . $e->getMessage();
                    }
                    ?>
                </tbody>
            </table>
            <?php
            try {
                $result = $conn->query("SELECT COUNT(*) as total FROM `$selected_table`");
                $row = $result->fetch_assoc();
                $total_pages = ceil($row['total'] / 50);
                ?>
                <div class='pagination'>
                    <?php
                    if ($page > 1) {
                        echo "<a href='?page=" . ($page - 1) . "'>Previous</a>";
                    }
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $start_page + 3);
                    if ($start_page > 1) {
                        echo "<a href='?page=1'>1</a>";
                        if ($start_page > 2) {
                            echo "...";
                        }
                    }
                    for ($i = $start_page; $i <= $end_page; $i++) {
                        if ($i == $page) {
                            echo "<a class='active' href='?page=$i'>$i</a>";
                        } else {
                            echo "<a href='?page=$i'>$i</a>";
                        }
                    }
                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) {
                            echo "...";
                        }
                        echo "<a href='?page=$total_pages'>$total_pages</a>";
                    }
                    if ($page < $total_pages) {
                        echo "<a href='?page=" . ($page + 1) . "'>Next</a>";
                    }
                    ?>
                </div>
                <?php
            } catch (mysqli_sql_exception $e) {
                echo "Error: " . $e->getMessage();
            }
        }
        ?>
    </div>
</div>
</body>
</html>
