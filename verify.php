<?php
session_start();
require_once 'db_connect.php';

// Ensure a reset request exists
if (!isset($_SESSION['reset_user_id'])) {
    die("❌ No reset request found. Please request again.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $otp = trim($_POST['otp']);
    $user_id = $_SESSION['reset_user_id'];

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

            // Allow password reset
            $_SESSION['otp_verified'] = true;
            header("Location: reset_password.php");
            exit;
        }
    } else {
        echo "❌ Invalid OTP. Try again.";
    }
}
?>

<!-- OTP Input Form -->
<form method="POST">
    <label>Enter the OTP sent to your email:</label><br>
    <input type="text" name="otp" maxlength="6" required>
    <button type="submit">Verify OTP</button>
</form>
