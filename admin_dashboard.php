<?php
session_start();
require_once 'db_connect.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){ header('Location: login.php'); exit; }
$name = $_SESSION['name'];

$rooms = $conn->query("SELECT * FROM rooms ORDER BY room_id ASC")->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Admin Dashboard — Weston Hotel</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{font-family:Poppins, sans-serif;background:#f5f8ff}.topbar{background:#0d6efd;color:#fff;padding:12px}</style>
</head>
<body>
<div class="topbar d-flex justify-content-between align-items-center">
  <div>Weston Hotel — Admin: <?=htmlspecialchars($name)?></div>
  <div><a href="view_reservation.php" class="btn btn-light btn-sm">Reservations</a> <a href="logout.php" class="btn btn-danger btn-sm">Logout</a></div>
</div>
<div class="container mt-4">
  <h3>Rooms</h3>
  <a href="add_room.php" class="btn btn-primary mb-2">Add Room</a>
  <table class="table table-striped bg-white">
    <thead class="table-primary"><tr><th>ID</th><th>Type</th><th>Price</th><th>Status</th><th>Qty</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach($rooms as $r): ?>
      <tr>
        <td><?= $r['room_id'] ?></td>
        <td><?= htmlspecialchars($r['room_type']) ?></td>
        <td><?= number_format($r['price'],2) ?></td>
        <td><?= htmlspecialchars($r['availability_status']) ?></td>
        <td><?= intval($r['quantity']) ?></td>
        <td>
          <a href="edit_room.php?id=<?= $r['room_id'] ?>" class="btn btn-sm btn-success">Edit</a>
          <a href="delete_room.php?id=<?= $r['room_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
