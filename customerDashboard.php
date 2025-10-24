<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'db_connect.php';

// Handle booking form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['book_room'])) {
    $room_type = trim($_POST['room_type']);
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $user_id = $_SESSION['user_id'];

    try {
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_type, check_in, check_out, status)
                                VALUES (:user_id, :room_type, :check_in, :check_out, 'Pending')");
        $stmt->execute([
            ':user_id' => $user_id,
            ':room_type' => $room_type,
            ':check_in' => $check_in,
            ':check_out' => $check_out
        ]);

        $success = "✅ Room booked successfully! Our team will confirm your reservation soon.";
    } catch (Exception $e) {
        $error = "❌ Booking failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard | Weston Hotel</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f9ff;
            color: #333;
        }

        .navbar {
            background-color: #007bff;
            color: white;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar h1 {
            font-size: 20px;
            margin: 0;
        }

        .navbar .user {
            font-size: 14px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            margin-left: 20px;
            background: rgba(255,255,255,0.2);
            padding: 8px 14px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .navbar a:hover {
            background: rgba(255,255,255,0.4);
        }

        .container {
            display: flex;
            height: calc(100vh - 60px);
        }

        .sidebar {
            width: 230px;
            background-color: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            padding: 25px 15px;
        }

        .sidebar h3 {
            color: #007bff;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .sidebar a {
            display: block;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
            margin-bottom: 8px;
            border-radius: 6px;
            transition: 0.3s;
            font-size: 14px;
        }

        .sidebar a:hover {
            background-color: #eaf3ff;
            color: #007bff;
        }

        .main {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .welcome {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        .welcome h2 {
            color: #007bff;
            margin-top: 0;
        }

        .book-form {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .book-form h3 {
            color: #007bff;
            margin-bottom: 15px;
        }

        .book-form label {
            font-weight: 600;
        }

        .book-form input, .book-form select {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .book-form button {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .book-form button:hover {
            background: #0056b3;
        }

        .message {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }

        .success { background: #e3fcec; color: #0f5132; border: 1px solid #badbcc; }
        .error { background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }

        .footer {
            text-align: center;
            font-size: 13px;
            color: #666;
            padding: 15px;
            background: #f4f9ff;
            border-top: 1px solid #e0e0e0;
        }

        @media(max-width: 768px) {
            .sidebar {
                display: none;
            }
            .container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>🏨 Weston Hotel</h1>
        <div class="user">
            Hi, <strong><?= htmlspecialchars($_SESSION['name']); ?></strong>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="sidebar">
            <h3>Menu</h3>
            <a href="#">🏠 Dashboard</a>
            <a href="#">📅 My Bookings</a>
            <a href="#">💳 Payments</a>
            <a href="#">⚙️ Account Settings</a>
        </div>

        <div class="main">
            <div class="welcome">
                <h2>Welcome, <?= htmlspecialchars($_SESSION['name']); ?>!</h2>
                <p>✅ You can book your stay and manage your reservations below.</p>
            </div>

            <div class="book-form">
                <h3>Book a Room</h3>

                <?php if (!empty($success)) echo "<div class='message success'>$success</div>"; ?>
                <?php if (!empty($error)) echo "<div class='message error'>$error</div>"; ?>

                <form method="POST">
                    <label>Room Type</label>
                    <select name="room_type" required>
                        <option value="">-- Select Room Type --</option>
                        <option value="Single">Single Room</option>
                        <option value="Double">Double Room</option>
                        <option value="Suite">Suite</option>
                        <option value="Deluxe">Deluxe</option>
                    </select>

                    <label>Check-in Date</label>
                    <input type="date" name="check_in" required>

                    <label>Check-out Date</label>
                    <input type="date" name="check_out" required>

                    <button type="submit" name="book_room">Book Now</button>
                </form>
            </div>
        </div>
    </div>

    <div class="footer">
        © <?= date("Y"); ?> Weston Hotel. All rights reserved.
    </div>
</body>
</html>
