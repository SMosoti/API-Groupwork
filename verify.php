<?php
session_start();
require_once 'db_connect.php';

// Ensure a signup request exists
if (!isset($_SESSION['signup_user_id'])) {
    die("❌ No signup request found. Please sign up again.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $otp = trim($_POST['otp']);
    $user_id = $_SESSION['signup_user_id'];

    // Fetch latest OTP for this user
    $stmt = $conn->prepare("SELECT * FROM otp_codes 
                            WHERE user_id = :user_id 
                            AND code = :code 
                            AND used = FALSE
                            ORDER BY id DESC LIMIT 1");
    $stmt->execute([
        ':user_id' => $user_id,
        ':code' => $otp
    ]);
    $otpRow = $stmt->fetch();

    if ($otpRow) {
        $current_time = date("Y-m-d H:i:s");

        if ($current_time > $otpRow['expires_at']) {
            echo "❌ OTP expired. Please request a new one.";
        } else {
            // Mark OTP as used
            $update = $conn->prepare("UPDATE otp_codes SET used = TRUE WHERE id = :id");
            $update->execute([':id' => $otpRow['id']]);

            echo "✅ OTP verified successfully! Your account is now active. <a href='login.php'>Login here</a>";

            // Clean session
            unset($_SESSION['signup_user_id']);
        }
    } else {
        echo "❌ Invalid OTP. Try again.";
    }
}
?>

<!-- OTP Input Form -->
<form method="POST" style="max-width:400px;margin:40px auto;border:1px solid #ccc;padding:20px;border-radius:8px;">
    <h2>Enter OTP</h2>
    <label>Enter the OTP sent to your email:</label><br>
    <input type="text" name="otp" maxlength="6" required style="width:100%;padding:8px;margin:5px 0;"><br>
    <button type="submit" style="background:#007bff;color:white;padding:10px 15px;border:none;border-radius:5px;">Verify OTP</button>
</form>
