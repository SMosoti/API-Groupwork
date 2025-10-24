<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['temp_user_id'])) {
    die("No login session found. Please login again.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $otp = trim($_POST['otp']);
    $user_id = $_SESSION['temp_user_id'];

    $stmt = $conn->prepare("SELECT * FROM otp_codes 
                            WHERE user_id = :user_id 
                            AND code = :code 
                            AND used = FALSE
                            ORDER BY id DESC LIMIT 1");
    $stmt->execute([':user_id' => $user_id, ':code' => $otp]);
    $otpRow = $stmt->fetch();

    if ($otpRow) {
        if (strtotime($otpRow['expires_at']) < time()) {
            $error = "❌ OTP expired. Please log in again.";
        } else {
            $update = $conn->prepare("UPDATE otp_codes SET used = TRUE WHERE id = :id");
            $update->execute([':id' => $otpRow['id']]);

            $_SESSION['user_id'] = $user_id;
            $_SESSION['name'] = $_SESSION['temp_name'];
            $_SESSION['email'] = $_SESSION['temp_email'];

            unset($_SESSION['temp_user_id'], $_SESSION['temp_name'], $_SESSION['temp_email']);

            header("Location: customerDashboard.php");
            exit;
        }
    } else {
        $error = "❌ Invalid OTP.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Verify OTP</title>
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #e6f3ff;
    display: flex; align-items: center; justify-content: center;
    height: 100vh; margin: 0;
}
.container {
    background: white;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    width: 350px;
    text-align: center;
}
input {
    width: 90%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 8px;
}
button {
    background: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    width: 100%;
}
button:hover { background: #0056b3; }
</style>
</head>
<body>
<div class="container">
    <h2>🔐 Verify OTP</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="otp" placeholder="Enter OTP" maxlength="6" required><br>
        <button type="submit">Verify</button>
    </form>
</div>
</body>
</html>
