<?php
include 'db_connect.php';
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role']!='client'){ die("❌ Access denied"); }

if($_SERVER['REQUEST_METHOD']=='POST'){
    $res_id = $_POST['reservation_id'];
    $user_id = $_SESSION['user_id'];

    // Fetch reservation
    $stmt = $conn->prepare("SELECT * FROM reservations WHERE reservation_id=:rid AND user_id=:uid");
    $stmt->execute([':rid'=>$res_id, ':uid'=>$user_id]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$res) die("❌ Reservation not found.");

    $rooms = json_decode($res['room_ids'], true);
    $num_rooms = array_sum($rooms);
    $created = new DateTime($res['created_at']);
    $now = new DateTime();
    $hours = ($now->getTimestamp() - $created->getTimestamp()) / 3600;

    $refund = $res['total_price'];
    if($hours > 10){
        $refund = $refund * (1 - 0.10); // Deduct 10%
    }

    // Update room quantities
    foreach($rooms as $rid=>$qty){
        $stmt = $conn->prepare("UPDATE rooms SET quantity=quantity+:qty, availability_status='available' WHERE room_id=:rid");
        $stmt->execute([':qty'=>$qty, ':rid'=>$rid]);
    }

    // Delete reservation
    $stmt = $conn->prepare("DELETE FROM reservations WHERE reservation_id=:rid");
    $stmt->execute([':rid'=>$res_id]);

    echo "✅ Reservation cancelled. Refund amount: KES ".number_format($refund,2)."<br>";
    echo "<a href='cancel_reservation.php'>Back to Reservations</a>";
}
?>
