<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php';

echo "<h1>💻 Hotel Management System Auto-Test & Sample Data Setup</h1>";

// 1️⃣ Test database connection
try {
    $conn->query("SELECT 1");
    echo "✅ Database connected!<br><br>";
} catch(Exception $e){
    die("❌ Database connection failed: ".$e->getMessage());
}

// 2️⃣ Check tables
$tables = ['users','rooms','reservations'];
foreach($tables as $table){
    $stmt = $conn->query("SHOW TABLES LIKE '$table'");
    if($stmt->rowCount() > 0){
        echo "✅ Table $table exists.<br>";
    } else {
        echo "❌ Table $table NOT found.<br>";
    }
}

// 3️⃣ Insert sample data if empty
// Users
$stmt = $conn->query("SELECT * FROM users");
if($stmt->rowCount() == 0){
    $conn->exec("INSERT INTO users (name,email,password,role) VALUES
        ('Diana Wambugha','dianawambugha22@gmail.com','".password_hash('password123', PASSWORD_DEFAULT)."','client'),
        ('Admin User','admin@example.com','".password_hash('adminpass', PASSWORD_DEFAULT)."','admin')
    ");
}

// Rooms
$stmt = $conn->query("SELECT * FROM rooms");
if($stmt->rowCount() == 0){
    $conn->exec("INSERT INTO rooms (room_type,price,availability_status) VALUES
        ('Single',5000,'available'),
        ('Double',8000,'available'),
        ('Suite',15000,'available')
    ");
}

// Reservations
$stmt = $conn->query("SELECT * FROM reservations");
if($stmt->rowCount() == 0){
    echo "📅 Reservations Table Sample:<br>❌ No reservations found.<br><br>";
}

// 4️⃣ Show sample users
$stmt = $conn->query("SELECT id,name,email,role FROM users");
echo "<h3>👥 Users Table Sample:</h3>";
echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
foreach($stmt as $row){
    echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['email']}</td><td>{$row['role']}</td></tr>";
}
echo "</table><br>";

// 5️⃣ Show sample rooms
$stmt = $conn->query("SELECT room_id,room_type,price,availability_status FROM rooms");
echo "<h3>🏨 Rooms Table Sample:</h3>";
echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Type</th><th>Price</th><th>Availability</th></tr>";
foreach($stmt as $row){
    echo "<tr><td>{$row['room_id']}</td><td>{$row['room_type']}</td><td>{$row['price']}</td><td>{$row['availability_status']}</td></tr>";
}
echo "</table><br>";

// 6️⃣ Show reservations
$stmt = $conn->query("SELECT * FROM reservations");
echo "<h3>📅 Reservations Table Sample:</h3>";
if($stmt->rowCount() > 0){
    echo "<table border='1' cellpadding='5'><tr>
            <th>ID</th><th>User ID</th><th>Room IDs</th><th>Booking Type</th>
            <th>Check-in</th><th>Check-out</th><th>Total Price</th><th>Discount Applied</th><th>Paid</th></tr>";
    foreach($stmt as $row){
        echo "<tr>
            <td>{$row['reservation_id']}</td>
            <td>{$row['user_id']}</td>
            <td>{$row['room_ids']}</td>
            <td>{$row['booking_type']}</td>
            <td>{$row['check_in_date']}</td>
            <td>{$row['check_out_date']}</td>
            <td>{$row['total_price']}</td>
            <td>{$row['discount_applied']}</td>
            <td>{$row['payment_status']}</td>
        </tr>";
    }
    echo "</table><br>";
} else {
    echo "❌ No reservations found.<br><br>";
}

echo "✅ System Test Complete!";
?>
