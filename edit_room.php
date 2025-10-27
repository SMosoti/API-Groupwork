<?php
session_start();
require_once 'db_connect.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='admin'){ header('Location: login.php'); exit; }

$id = intval($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id=:id");
$stmt->execute([':id'=>$id]);
$r = $stmt->fetch();
if(!$r) { echo "Room not found"; exit; }

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $type = trim($_POST['room_type']);
    $price = floatval($_POST['price']);
    $qty = intval($_POST['quantity']);
    $status = $_POST['availability_status'];
    $up = $conn->prepare("UPDATE rooms SET room_type=:type, price=:price, quantity=:qty, availability_status=:status WHERE room_id=:id");
    $up->execute([':type'=>$type,':price'=>$price,':qty'=>$qty,':status'=>$status,':id'=>$id]);
    header('Location: admin_dashboard.php'); exit;
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Edit Room</title></head><body>
<div style="max-width:420px;margin:40px auto">
<h3>Edit Room <?= htmlspecialchars($r['room_type']) ?></h3>
<form method="POST">
<label>Type</label><br><input name="room_type" value="<?=htmlspecialchars($r['room_type'])?>" required><br>
<label>Price</label><br><input name="price" type="number" step="0.01" value="<?=$r['price']?>" required><br>
<label>Quantity</label><br><input name="quantity" type="number" min="0" value="<?=$r['quantity']?>" required><br>
<label>Status</label><br>
<select name="availability_status">
  <option value="available" <?=$r['availability_status']=='available'?'selected':''?>>Available</option>
  <option value="reserved" <?=$r['availability_status']=='reserved'?'selected':''?>>Reserved</option>
</select><br><br>
<button type="submit" style="background:#0d47a1;color:#fff;padding:8px;border:none;border-radius:6px">Save</button>
<a href="admin_dashboard.php" style="margin-left:10px">Back</a>
</form>
</div></body></html>
