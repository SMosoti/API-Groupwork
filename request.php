<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate OTP
        $otp = rand(100000, 999999);
        $expires_at = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        // Insert OTP into DB
        $stmt = $conn->prepare("INSERT INTO otp_codes (user_id, code, expires_at) 
                                VALUES (:user_id, :code, :expires_at)");
        $stmt->execute([
            ':user_id' => $user['id'],
            ':code' => $otp,
            ':expires_at' => $expires_at
        ]);

        // ⚡ You can use PHPMailer or mail() for now
        // mail($email, "Your Password Reset Code", "Your OTP is: $otp");

        echo "✅ OTP has been sent to your email.";
        $_SESSION['reset_user_id'] = $user['id']; // save for next step
    } else {
        echo "❌ No account found with this email.";
    }
}
?>

<!-- Simple Form -->
<form method="POST">
    <label>Enter your email to reset password:</label><br>
    <input type="email" name="email" required>
    <button type="submit">Send OTP</button>
</form>
