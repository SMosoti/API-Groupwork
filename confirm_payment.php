<?php
session_start();
require_once 'db_connect.php';
if (!isset($_SESSION['user_id'])) header("Location: login.php");

$reservation_id = $_GET['reservation_id'] ?? null;
if (!$reservation_id) die("Missing reservation ID.");

// Fetch reservation details
$stmt = $conn->prepare("SELECT * FROM reservations WHERE reservation_id = ?");
$stmt->execute([$reservation_id]);
$res = $stmt->fetch();

if (!$res) die("Reservation not found.");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Confirm Payment - Weston Hotel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card p-4 shadow-lg">
    <h3>Confirm Payment</h3>
    <p><strong>Total Amount:</strong> KES <?= number_format($res['total_price'], 2) ?></p>
    <p><strong>Discount Applied:</strong> KES <?= number_format($res['discount_applied'], 2) ?></p>
    <form method="POST" action="process_payment.php">
      <input type="hidden" name="reservation_id" value="<?= $res['reservation_id'] ?>">
      <div class="mb-3">
        <label class="form-label">Enter M-PESA Phone Number:</label>
        <input type="text" name="phone" class="form-control" placeholder="2547XXXXXXXX" required>
      </div>
      <button type="submit" class="btn btn-success w-100">Pay Now</button>
    </form>
  </div>
</div>
</body>
</html>
