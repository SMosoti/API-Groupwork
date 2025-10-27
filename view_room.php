<?php
session_start();
require_once 'db_connect.php';
if (!isset($_SESSION['user_id'])) header('Location: login.php');

$role = $_SESSION['role'] ?? 'client';
$name = $_SESSION['name'] ?? 'Guest';

// Default dates
$check_in  = $_GET['check_in']  ?? date('Y-m-d');
$check_out = $_GET['check_out'] ?? date('Y-m-d', strtotime('+1 day'));

// Fetch rooms
$rooms = $conn->query("SELECT * FROM rooms ORDER BY room_id ASC")->fetchAll();

// Get remaining availability for chosen range
function getAvailableQty($conn, $room_id, $check_in, $check_out, $default_qty) {
    $sql = "SELECT MIN(available_qty) AS min_avail 
            FROM room_availability 
            WHERE room_id = ? AND date BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$room_id, $check_in, $check_out]);
    $row = $stmt->fetch();
    return $row && $row['min_avail'] !== null ? $row['min_avail'] : $default_qty;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Rooms – Weston Hotel</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{font-family:Poppins;background:#f5f8ff}</style>
</head>
<body>
<nav class="navbar navbar-expand bg-primary text-light p-3">
  <div class="container">
    <strong>Weston Hotel</strong> — Welcome, <?=htmlspecialchars($name)?>
    <div class="ms-auto">
      <a href="view_reservation.php" class="btn btn-light btn-sm">Reservations</a>
      <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <h3>Rooms</h3>
  <form method="get" class="d-flex mb-3">
    <div class="me-3">
      <label>Check-in:</label>
      <input type="date" name="check_in" value="<?=$check_in?>" class="form-control">
    </div>
    <div class="me-3">
      <label>Check-out:</label>
      <input type="date" name="check_out" value="<?=$check_out?>" class="form-control">
    </div>
    <div class="align-self-end">
      <button class="btn btn-primary">Search Availability</button>
    </div>
  </form>

  <table class="table table-bordered bg-white">
    <thead class="table-primary"><tr><th>Type</th><th>Price</th><th>Available</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach($rooms as $r): 
        $available = getAvailableQty($conn, $r['room_id'], $check_in, $check_out, $r['quantity']);
    ?>
      <tr>
        <td><?=htmlspecialchars($r['room_type'])?></td>
        <td><?=number_format($r['price'],2)?></td>
        <td><?=$available?></td>
        <td>
          <?php if($role==='client' && $available>0): ?>
            <form method="POST" action="book_room.php" class="d-flex">
              <input type="hidden" name="room_id" value="<?=$r['room_id']?>">
              <input type="hidden" name="check_in" value="<?=$check_in?>">
              <input type="hidden" name="check_out" value="<?=$check_out?>">
              <input type="number" name="quantity" min="1" max="<?=$available?>" value="1" class="form-control form-control-sm me-2" style="width:80px">
              <button class="btn btn-success btn-sm">Book</button>
            </form>
          <?php else: ?><span class="text-muted">N/A</span><?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
