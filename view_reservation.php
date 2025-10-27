<?php
session_start();
require_once 'db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch reservations for this user
try {
    $stmt = $conn->prepare("
        SELECT r.reservation_id, r.room_ids, r.check_in_date, r.check_out_date, r.total_price,
               r.payment_status, r.cancellation_status, rm.room_type, rm.price
        FROM reservations r
        JOIN rooms rm ON FIND_IN_SET(rm.room_id, r.room_ids)
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $reservations = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching reservations: " . $e->getMessage());
}

// Handle cancellation
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $reservation_id = $_GET['cancel'];
    try {
        $update = $conn->prepare("UPDATE reservations SET cancellation_status = 'cancelled' WHERE reservation_id = ? AND user_id = ?");
        $update->execute([$reservation_id, $user_id]);
        echo "<script>alert('Reservation cancelled successfully!'); window.location='view_reservation.php';</script>";
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Error cancelling reservation: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Reservations | Weston Hotel</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f6ff;
            color: #003366;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        h2 {
            background-color: #003366;
            color: white;
            padding: 15px 0;
        }
        table {
            width: 85%;
            margin: 25px auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #cce0ff;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9fbff;
        }
        a.button {
            text-decoration: none;
            background: #007bff;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
        }
        a.button:hover {
            background: #0056b3;
        }
        .cancelled {
            color: red;
            font-weight: bold;
        }
        .paid {
            color: green;
            font-weight: bold;
        }
        a.nav {
            margin: 10px;
            display: inline-block;
            color: #007bff;
            text-decoration: none;
        }
        a.nav:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h2>My Reservations</h2>

<?php if (empty($reservations)): ?>
    <p>You have no reservations yet.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Room Type</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>Total Price (KES)</th>
            <th>Payment Status</th>
            <th>Cancellation Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($reservations as $res): ?>
        <tr>
            <td><?= htmlspecialchars($res['room_type']) ?></td>
            <td><?= htmlspecialchars($res['check_in_date']) ?></td>
            <td><?= htmlspecialchars($res['check_out_date']) ?></td>
            <td><?= number_format($res['price'], 2) ?></td>
            <td class="<?= $res['payment_status'] === 'paid' ? 'paid' : '' ?>">
                <?= htmlspecialchars($res['payment_status']) ?>
            </td>
            <td class="<?= $res['cancellation_status'] === 'cancelled' ? 'cancelled' : '' ?>">
                <?= htmlspecialchars($res['cancellation_status']) ?>
            </td>
            <td>
                <?php if ($res['cancellation_status'] !== 'cancelled'): ?>
                    <a href="?cancel=<?= $res['reservation_id'] ?>" class="button" onclick="return confirm('Cancel this reservation?')">Cancel</a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<a href="book_room.php" class="nav">Book Another Room</a> |
<a href="logout.php" class="nav">Logout</a>

</body>
</html>
