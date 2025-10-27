<?php
session_start();
require_once 'db_connect.php';
if(!isset($_SESSION['user_id'])) header('Location: login.php');

$id = intval($_GET['id'] ?? 0);
if(!$id) die("Invalid id");

$stmt = $conn->prepare("SELECT * FROM reservations WHERE reservation_id=:id");
$stmt->execute([':id'=>$id]); $res = $stmt->fetch();
if(!$res) die("Reservation not found");

// permission
if($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $res['user_id']) die("Access denied");

// hours since created
$hours = (time() - strtotime($res['created_at']))/3600;
$penalty = 0;
if($hours > 10){
    // 10% of total
    $penalty = 0.10 * $res['total_price'];
}
$refund = $res['total_price'] - $penalty;

// mark cancelled
$conn->prepare("UPDATE reservations SET payment_status='cancelled' WHERE reservation_id=:id")->execute([':id'=>$id]);
// log payment negative amount to payments as refund
$conn->prepare("INSERT INTO payments (reservation_id, phone_number, amount, status) VALUES (:rid,'N/A',:amt,'success')")->execute([':rid'=>$id,':amt'=>-1*$refund]);

// restore rooms
$roomData = json_decode($res['room_ids'], true);
if(!$roomData){
    $arr = array_filter(explode(',', $res['room_ids']));
    foreach($arr as $rid) $conn->prepare("UPDATE rooms SET quantity = quantity + 1, availability_status='available' WHERE room_id=:id")->execute([':id'=>$rid]);
} else {
    foreach($roomData as $rid=>$q) $conn->prepare("UPDATE rooms SET quantity = quantity + :q, availability_status='available' WHERE room_id=:id")->execute([':q'=>$q,':id'=>$rid]);
}

echo "<script>alert('Reservation cancelled. Refund: KES ".number_format($refund,2)." (Penalty: KES ".number_format($penalty,2).")'); window.location='view_reservation.php';</script>";
exit;
