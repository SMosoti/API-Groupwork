<?php
session_start();
require_once 'db_connect.php';

// Ensure OTP was verified
if (!isset($_SESSION['reset_user_id']) || !isset($_SESSION['otp_verified'])) {
    die("❌ Unauthorized access. Please request reset again.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        echo "❌ Passwords do not match.";
    } else {
        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Update in DB
        $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $_SESSION['reset_user_id']
        ]);

        // Clear session vars
        unset($_SESSION['reset_user_id']);
        unset($_SESSION['otp_verified']);

        echo "✅ Password reset successful. You can now <a href='login.php'>login</a> with your new credentials.";
    }
}
?>

<!-- Reset Password Form -->
<form method="POST">
    <label>New Password:</label><br>
    <input type="password" name="password" required><br><br>

    <label>Confirm New Password:</label><br>
    <input type="password" name="confirm_password" required><br><br>

    <button type="submit">Reset Password</button>
</form>
