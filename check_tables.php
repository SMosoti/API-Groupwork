<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

$dbname = 'api_groupwork';

// Fetch tables
$tablesStmt = $conn->query("SHOW TABLES");
$tables = $tablesStmt->fetchAll(PDO::FETCH_NUM);

echo "<h2>Database: $dbname</h2>";

if (!$tables) {
    echo "❌ No tables found!";
    exit;
}

foreach ($tables as $tableRow) {
    $tableName = $tableRow[0];
    echo "<hr>";
    echo "<h3>Table: $tableName</h3>";

    // Fetch columns
    $columnsStmt = $conn->query("SHOW COLUMNS FROM `$tableName`");
    $columns = $columnsStmt->fetchAll(PDO::FETCH_ASSOC);
    if ($columns) {
        echo "<b>Columns:</b><ul>";
        foreach ($columns as $col) {
            echo "<li>" . $col['Field'] . " — " . $col['Type'] . " — Null: " . $col['Null'] . " — Default: " . $col['Default'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "❌ Could not fetch columns.<br>";
    }

    // Fetch sample data
    $sampleStmt = $conn->query("SELECT * FROM `$tableName` LIMIT 5");
    $sampleData = $sampleStmt->fetchAll(PDO::FETCH_ASSOC);
    if ($sampleData) {
        echo "<b>Sample Data (up to 5 rows):</b><br>";
        echo "<table border='1' cellpadding='5'><tr>";
        // Table headers
        foreach (array_keys($sampleData[0]) as $header) {
            echo "<th>" . htmlspecialchars($header) . "</th>";
        }
        echo "</tr>";
        // Table rows
        foreach ($sampleData as $row) {
            echo "<tr>";
            foreach ($row as $val) {
                echo "<td>" . htmlspecialchars($val) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table><br>";
    } else {
        echo "❌ No data found in this table.<br>";
    }
}
?>
