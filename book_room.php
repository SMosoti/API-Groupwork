<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$room_id = $_POST['room_id'];
$quantity = intval($_POST['quantity']);
$booking_type = $_POST['booking_type'] ?? 'today';

// Fetch room details
$stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch();

if (!$room) {
    die("Room not found.");
}

$price = $room['price'];
$total = $price * $quantity;

// Apply discount if reserve booking
$discount = 0;
if ($booking_type === 'reserve' && $quantity >= 3) {
    $discount = 0.05 * $total; // 5% discount example
}
$final_total = $total - $discount;

// Booking dates
$check_in = $booking_type === 'today' ? date('Y-m-d') : ($_POST['check_in'] ?? date('Y-m-d'));
$check_out = $booking_type === 'today' ? date('Y-m-d') : ($_POST['check_out'] ?? date('Y-m-d', strtotime('+1 day')));

// Save reservation
$stmt = $conn->prepare("INSERT INTO reservations 
    (user_id, booking_type, room_ids, guests, check_in_date, check_out_date, total_price, discount_applied, payment_status, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");

$stmt->execute([$user_id, $booking_type, $room_id, $quantity, $check_in, $check_out, $final_total, $discount]);

$reservation_id = $conn->lastInsertId();

// ✅ Update room availability (reduce quantity)
$new_qty = $room['quantity'] - $quantity;
if ($new_qty < 0) $new_qty = 0;
$update = $conn->prepare("UPDATE rooms SET quantity = ?, availability_status = IF(?=0, 'reserved', 'available') WHERE room_id = ?");
$update->execute([$new_qty, $new_qty, $room_id]);

// ✅ Send STK Push prompt instead of redirect
echo "<script>
alert('Payment prompt sent to your phone. Please complete the payment.');
window.location = 'pay_now.php';
</script>";
exit;

