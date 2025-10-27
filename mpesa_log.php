<?php
session_start();
require_once 'db_connect.php';
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit;
}
$result = $conn->query("SELECT * FROM mpesa_logs ORDER BY date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>M-PESA Logs</title>
<style>
body { font-family: Poppins, sans-serif; background: #f0f6ff; }
h2 { text-align: center; color: #004aad; margin-top: 20px; }
table {
    width: 80%; margin: 30px auto; border-collapse: collapse;
    background: white; border-radius: 10px; overflow: hidden;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
th, td { padding: 12px; text-align: center; border-bottom: 1px solid #ddd; }
th { background: #004aad; color: white; }
tr:hover { background: #e8f1ff; }
</style>
</head>
<body>
<h2>M-PESA Transaction Logs</h2>
<table>
<tr><th>ID</th><th>Phone</th><th>Amount</th><th>Transaction Code</th><th>Date</th></tr>
<?php while($row = $result->fetch_assoc()) { ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['phone'] ?></td>
<td><?= $row['amount'] ?></td>
<td><?= $row['transaction_code'] ?></td>
<td><?= $row['date'] ?></td>
</tr>
<?php } ?>
</table>
</body>
</html>
