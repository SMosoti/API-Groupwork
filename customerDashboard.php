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
<title>Dashboard</title>
<style>
body {
    font-family: 'Poppins', sans-serif;
    margin: 0; display: flex;
    background: #f4f9ff;
}
.sidebar {
    width: 220px;
    background: #007bff;
    color: white;
    height: 100vh;
    padding: 20px;
}
.sidebar h2 {
    font-size: 22px;
}
.sidebar a {
    display: block;
    color: white;
    padding: 10px 0;
    text-decoration: none;
}
.sidebar a:hover {
    background: #0056b3;
    border-radius: 5px;
}
.main {
    flex: 1;
    padding: 30px;
}
.card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}
</style>
</head>
<body>
<div class="sidebar">
    <h2>🏨 Riverside</h2>
    <a href="#">Dashboard</a>
    <a href="#">Book Room</a>
    <a href="#">My Reservations</a>
    <a href="logout.php">Logout</a>
</div>
<div class="main">
    <div class="card">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> 👋</h2>
        <p>You are securely logged in using 2FA (OTP).</p>
    </div>
</div>
</body>
</html>
