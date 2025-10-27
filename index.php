<?php
include 'db_connect.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$isAdmin = $_SESSION['role'] === 'admin';

$sql = $isAdmin ? "SELECT * FROM rooms" : "SELECT * FROM rooms WHERE availability_status='available'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rooms Dashboard</title>
</head>
<body style="font-family:Arial; max-width:800px;margin:20px auto;">
<h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
<a href="logout.php" style="margin-right:20px;">Logout</a>
<?php if($isAdmin){ ?><a href="add_room.php">Add New Room</a><?php } ?>
<hr>

<form method="POST" action="book_rooms.php">
<table border="1" cellpadding="10" style="width:100%;border-collapse:collapse;">
<tr>
    <?php if(!$isAdmin){ ?><th>Select</th><?php } ?>
    <th>Room ID</th>
    <th>Type</th>
    <th>Price</th>
    <th>Status</th>
    <?php if($isAdmin){ ?><th>Actions</th><?php } ?>
</tr>
<?php
if ($result->rowCount() > 0) {
    while($row = $result->fetch(PDO::FETCH_ASSOC)){
        echo "<tr>";
        if(!$isAdmin){
            echo "<td><input type='checkbox' name='room_ids[]' value='".$row['room_id']."'></td>";
        }
        echo "<td>".$row['room_id']."</td>";
        echo "<td>".$row['room_type']."</td>";
        echo "<td>".$row['price']."</td>";
        echo "<td>".$row['availability_status']."</td>";

        if($isAdmin){
            echo "<td><a href='edit_room.php?id=".$row['room_id']."'>Edit</a> | 
                      <a href='delete_room.php?id=".$row['room_id']."'>Delete</a></td>";
        }
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='".($isAdmin?6:5)."'>No rooms found</td></tr>";
}
?>
</table>

<?php if(!$isAdmin){ ?>
<br>
<label>Booking Type:</label><br>
<input type="radio" name="booking_type" value="today" checked> Book Today<br>
<input type="radio" name="booking_type" value="reserve"> Reserve for Later<br><br>

<label>Check-In Date:</label><br>
<input type="date" name="check_in_date" required><br><br>

<label>Check-Out Date:</label><br>
<input type="date" name="check_out_date" required><br><br>

<button type="submit" style="padding:10px 15px;background:#28a745;color:white;border:none;border-radius:5px;">Proceed</button>
</form>
<?php } ?>
</body>
</html>
