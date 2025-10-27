<?php
session_start();
require_once 'db_connect.php';

<<<<<<< HEAD
// Ensure a signup request exists
if (!isset($_SESSION['signup_user_id'])) {
    die("❌ No signup request found. Please sign up again.");
=======
if (!isset($_SESSION['temp_user_id'])) {
    die("No login session found. Please login again.");
>>>>>>> 60a6e252ca144b505ab40824e18cabeda9a7f0fe
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $otp = trim($_POST['otp']);
<<<<<<< HEAD
    $user_id = $_SESSION['signup_user_id'];
=======
    $user_id = $_SESSION['temp_user_id'];
>>>>>>> 60a6e252ca144b505ab40824e18cabeda9a7f0fe

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

<<<<<<< HEAD
            echo "✅ OTP verified successfully! Your account is now active. <a href='login.php'>Login here</a>";

            // Clean session
            unset($_SESSION['signup_user_id']);
=======
            $_SESSION['user_id'] = $user_id;
            $_SESSION['name'] = $_SESSION['temp_name'];
            $_SESSION['email'] = $_SESSION['temp_email'];

            unset($_SESSION['temp_user_id'], $_SESSION['temp_name'], $_SESSION['temp_email']);

            header("Location: customerDashboard.php");
            exit;
>>>>>>> 60a6e252ca144b505ab40824e18cabeda9a7f0fe
        }
    } else {
        $error = "❌ Invalid OTP.";
    }
}
?>

<<<<<<< HEAD
<!-- OTP Input Form -->
<form method="POST" style="max-width:400px;margin:40px auto;border:1px solid #ccc;padding:20px;border-radius:8px;">
    <h2>Enter OTP</h2>
    <label>Enter the OTP sent to your email:</label><br>
    <input type="text" name="otp" maxlength="6" required style="width:100%;padding:8px;margin:5px 0;"><br>
    <button type="submit" style="background:#007bff;color:white;padding:10px 15px;border:none;border-radius:5px;">Verify OTP</button>
</form>
=======
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
>>>>>>> 60a6e252ca144b505ab40824e18cabeda9a7f0fe
