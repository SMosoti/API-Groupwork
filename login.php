<?php
session_start();

require_once 'db_connect.php'; // your PostgreSQL connection file
require_once 'vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle form POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        // 1. Insert user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password) RETURNING id");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password
        ]);
        $user = $stmt->fetch();
        $userId = $user['id'];

        // 2. Generate OTP
        $otp = rand(100000, 999999);
        $expiresAt = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $stmt = $conn->prepare("INSERT INTO otp_codes (user_id, code, expires_at) VALUES (:user_id, :code, :expires_at)");
        $stmt->execute([
            ':user_id' => $userId,
            ':code' => $otp,
            ':expires_at' => $expiresAt
        ]);

        // 3. Send OTP email
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your_email@gmail.com'; // replace
        $mail->Password   = 'your_app_password';    // replace
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('your_email@gmail.com', 'Weston Hotel');
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body    = "<p>Dear $name,</p><p>Your OTP code is: <b>$otp</b></p><p>This code will expire in 10 minutes.</p>";

        $mail->send();

        $_SESSION['message'] = "Signup successful! Check your email for OTP.";
        header("Location: verify.php?user_id=$userId");
        exit;
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage();
    }
}