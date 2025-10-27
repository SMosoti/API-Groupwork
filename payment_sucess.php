<?php
session_start();
require_once 'db_connect.php';

if (!isset($_GET['reservation_id'])) {
    die("Reservation ID missing.");
}

$res_id = $_GET['reservation_id'];

// Fetch reservation details
$stmt = $conn->prepare("SELECT * FROM reservations WHERE reservation_id=?");
$stmt->execute([$res_id]);
$res = $stmt->fetch();

if (!$res) {
    die("Reservation not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment Successful - Weston Hotel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
      font-family: 'Poppins', sans-serif;
    }
    .success-box {
      max-width: 500px;
      margin: 80px auto;
      padding: 30px;
      background: white;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    .success-icon {
      font-size: 70px;
      color: #28a745;
    }
    .btn-home {
      background-color: #198754;
      color: white;
      border-radius: 30px;
      padding: 10px 30px;
      margin-top: 15px;
    }
  </style>
</head>
<body>

<div class="success-box">
  <div class="success-icon">✅</div>
  <h2 class="mt-3">Payment Successful!</h2>
  <p>Thank you for your payment, <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Guest') ?></strong>.</p>
  <p>Your reservation <strong>#<?= $res_id ?></strong> has been confirmed.</p>
  <hr>
  <p><strong>MPESA Code:</strong> <?= htmlspecialchars($res['mpesa_code'] ?? 'Processing...') ?></p>
  <p><strong>Amount Paid:</strong> KES <?= number_format($res['total_price'], 2) ?></p>
  <p><strong>Status:</strong> <?= ucfirst($res['payment_status']) ?></p>
  <a href="view_reservation.php" class="btn btn-home">View My Bookings</a>
</div>

</body>
</html>
