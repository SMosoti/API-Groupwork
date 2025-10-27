<?php
session_start();
require_once 'db_connect.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='admin'){ header('Location: login.php'); exit; }

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $type = trim($_POST['room_type']);
    $price = floatval($_POST['price']);
    $qty = intval($_POST['quantity']);
    $status = $_POST['availability_status'] ?? 'available';
    $ins = $conn->prepare("INSERT INTO rooms (room_type, price, availability_status, quantity) VALUES (:type,:price,:status,:qty)");
    $ins->execute([':type'=>$type,':price'=>$price,':status'=>$status,':qty'=>$qty]);
    header('Location: admin_dashboard.php'); exit;
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Add Room</title></head><body>
<div style="max-width:420px;margin:40px auto;font-family:Poppins">
<h3>Add Room</h3>
<form method="POST">
<label>Room type</label><br><input name="room_type" required><br>
<label>Price</label><br><input name="price" type="number" step="0.01" required><br>
<label>Quantity</label><br><input name="quantity" type="number" value="1" min="0"><br>
<label>Status</label><br>
<select name="availability_status"><option value="available">Available</option><option value="reserved">Reserved</option></select><br><br>
<button type="submit" style="background:#0d47a1;color:#fff;padding:8px;border:none;border-radius:6px">Add Room</button>
<a href="admin_dashboard.php" style="margin-left:10px">Back</a>
</form>
</div>
</body></html>
