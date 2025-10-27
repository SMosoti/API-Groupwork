<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Weston Hotel</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f9ff;
            color: #333;
        }

        /* ====== NAVBAR ====== */
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

        /* ====== LAYOUT ====== */
        .container {
            display: flex;
            height: calc(100vh - 60px);
        }

        /* ====== SIDEBAR ====== */
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

        /* ====== MAIN CONTENT ====== */
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

        /* ====== DASHBOARD CARDS ====== */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 25px;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .card h3 {
            margin: 10px 0;
            color: #007bff;
            font-size: 18px;
        }

        .card p {
            color: #555;
            font-size: 14px;
        }

        /* ====== FOOTER ====== */
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

    <!-- NAVBAR -->
    <div class="navbar">
        <h1>🏨 Weston Hotel Dashboard</h1>
        <div class="user">
            Hi, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="container">
        <div class="sidebar">
            <h3>Menu</h3>
            <a href="#">🏠 Dashboard</a>
            <a href="#">🛏️ Rooms</a>
            <a href="#">📅 Reservations</a>
            <a href="#">👥 Guests</a>
            <a href="#">💳 Payments</a>
            <a href="#">⚙️ Settings</a>
        </div>

        <div class="main">
            <div class="welcome">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
                <p>✅ You are now logged into the <b>Hotel Reservation System</b>.</p>
                <?php if (isset($_SESSION['role'])): ?>
                    <p><strong>Role:</strong> <?php echo htmlspecialchars($_SESSION['role']); ?></p>
                <?php endif; ?>
            </div>

            <div class="cards">
                <div class="card">
                    <h3>🛏️ Rooms</h3>
                    <p>View available rooms, add new ones, and manage occupancy.</p>
                </div>

                <div class="card">
                    <h3>📅 Reservations</h3>
                    <p>Check all current and upcoming bookings in real-time.</p>
                </div>

                <div class="card">
                    <h3>👥 Guests</h3>
                    <p>Manage guest profiles, history, and preferences.</p>
                </div>

                <div class="card">
                    <h3>💳 Payments</h3>
                    <p>Track payments, generate invoices, and manage refunds.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        © <?php echo date("Y"); ?> Weston Hotel. All rights reserved.
    </div>
</body>
</html>
