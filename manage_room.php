<?php
// manage_room.php
session_start();
require_once 'db_connect.php';

// admin-only
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit;
}

// Add room
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add'){
    $room_type = trim($_POST['room_type']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $status = $_POST['availability_status'] ?? 'available';

    $stmt = $conn->prepare("INSERT INTO rooms (room_type, price, availability_status, quantity) VALUES (:rt, :p, :st, :q)");
    $stmt->execute([':rt'=>$room_type, ':p'=>$price, ':st'=>$status, ':q'=>$quantity]);
    header("Location: manage_room.php");
    exit;
}

// Edit room
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit'){
    $room_id = intval($_POST['room_id']);
    $room_type = trim($_POST['room_type']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $status = $_POST['availability_status'] ?? 'available';

    $stmt = $conn->prepare("UPDATE rooms SET room_type=:rt, price=:p, availability_status=:st, quantity=:q WHERE room_id=:id");
    $stmt->execute([':rt'=>$room_type, ':p'=>$price, ':st'=>$status, ':q'=>$quantity, ':id'=>$room_id]);
    header("Location: manage_room.php");
    exit;
}

// Delete room (via GET)
if(isset($_GET['delete'])){
    $del = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM rooms WHERE room_id=:id");
    $stmt->execute([':id'=>$del]);
    header("Location: manage_room.php");
    exit;
}

// Fetch rooms
$stmt = $conn->query("SELECT * FROM rooms ORDER BY room_id ASC");
$rooms = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Manage Room - Admin | Weston Hotel</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f3f7fb; }
    .header { background: linear-gradient(90deg,#0d6efd,#0b5ed7); color:#fff; padding:18px; }
    .card-room { border-radius:12px; }
    .btn-edit { background:#0d6efd; color:#fff; }
    .btn-delete { background:#dc3545; color:#fff; }
    .muted { color:#6c757d; }
  </style>
</head>
<body>
  <header class="header text-center">
    <div class="container">
      <h2 class="mb-0">Admin — Manage Room</h2>
      <small>Welcome, <?=htmlspecialchars($_SESSION['name'])?> | <a href="admin_dashboard.php" class="text-white">Dashboard</a> | <a href="logout.php" class="text-white">Logout</a></small>
    </div>
  </header>

  <main class="container py-4">
    <!-- Add Room -->
    <div class="card mb-4 shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Add New Room</h5>
        <form method="POST" class="row g-2 align-items-end">
          <input type="hidden" name="action" value="add">
          <div class="col-md-4">
            <label class="form-label">Room Type</label>
            <input name="room_type" class="form-control" required>
          </div>
          <div class="col-md-2">
            <label class="form-label">Price (KES)</label>
            <input name="price" type="number" step="0.01" class="form-control" required>
          </div>
          <div class="col-md-2">
            <label class="form-label">Quantity</label>
            <input name="quantity" type="number" min="0" class="form-control" value="1" required>
          </div>
          <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="availability_status" class="form-select">
              <option value="available">Available</option>
              <option value="reserved">Reserved</option>
            </select>
          </div>
          <div class="col-md-2">
            <button class="btn btn-success w-100" type="submit">+ Add Room</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Rooms grid -->
    <div class="row">
      <?php if(count($rooms)===0): ?>
        <div class="col-12"><div class="alert alert-info">No rooms found. Add rooms above.</div></div>
      <?php endif; ?>

      <?php foreach($rooms as $r): ?>
        <div class="col-md-4 mb-3">
          <div class="card card-room shadow-sm">
            <div class="card-body">
              <h5 class="card-title"><?=htmlspecialchars($r['room_type'])?></h5>
              <p class="card-text">
                <strong>Price:</strong> KES <?=number_format($r['price'],2)?><br>
                <strong>Available:</strong> <span class="muted"><?=intval($r['quantity'])?></span><br>
                <strong>Status:</strong> <?=htmlspecialchars($r['availability_status'])?>
              </p>

              <div class="d-flex justify-content-between">
                <!-- Edit: launch modal -->
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?=$r['room_id']?>">Edit</button>

                <!-- Delete -->
                <a href="manage_room.php?delete=<?=$r['room_id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this room?')">Delete</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal<?=$r['room_id']?>" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <form method="POST" class="needs-validation" novalidate>
                <div class="modal-header">
                  <h5 class="modal-title">Edit Room #<?=$r['room_id']?></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="action" value="edit">
                  <input type="hidden" name="room_id" value="<?=$r['room_id']?>">
                  <div class="mb-2">
                    <label class="form-label">Room Type</label>
                    <input name="room_type" class="form-control" value="<?=htmlspecialchars($r['room_type'])?>" required>
                  </div>
                  <div class="mb-2">
                    <label class="form-label">Price (KES)</label>
                    <input name="price" type="number" step="0.01" class="form-control" value="<?=htmlspecialchars($r['price'])?>" required>
                  </div>
                  <div class="mb-2">
                    <label class="form-label">Quantity</label>
                    <input name="quantity" type="number" min="0" class="form-control" value="<?=intval($r['quantity'])?>" required>
                  </div>
                  <div class="mb-2">
                    <label class="form-label">Status</label>
                    <select name="availability_status" class="form-select">
                      <option value="available" <?= $r['availability_status']=='available' ? 'selected' : '' ?>>Available</option>
                      <option value="reserved" <?= $r['availability_status']=='reserved' ? 'selected' : '' ?>>Reserved</option>
                    </select>
                  </div>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-primary" type="submit">Save</button>
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
              </form>
            </div>
          </div>
        </div>

      <?php endforeach; ?>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
