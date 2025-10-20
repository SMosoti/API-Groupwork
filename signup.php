<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once 'db_connect.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        // Check if user already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $check->execute([':email' => $email]);
        if ($check->fetch()) {
            echo "❌ Account already exists. <a href='login.php'>Login instead</a>";
            exit;
        }

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password) RETURNING id");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password
        ]);
        $user = $stmt->fetch();
        $userId = $user['id'];

        // Generate OTP
        $otp = rand(100000, 999999);
        $expiresAt = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $stmt = $conn->prepare("INSERT INTO otp_codes (user_id, code, expires_at, used) 
                                VALUES (:user_id, :code, :expires_at, FALSE)");
        $stmt->execute([
            ':user_id' => $userId,
            ':code' => $otp,
            ':expires_at' => $expiresAt
        ]);

        // Send OTP Email
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your_email@gmail.com'; // change
        $mail->Password   = 'your_app_password';    // change (use app password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('your_email@gmail.com', 'Weston Hotel');
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body    = "<p>Dear $name,</p>
                          <p>Your OTP code is: <b>$otp</b></p>
                          <p>This code expires in 10 minutes.</p>";

        $mail->send();

        $_SESSION['signup_user_id'] = $userId;
        header("Location: verify.php");
        exit;

    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage();
    }
}
?>

<!-- HTML Signup Form -->
<form method="POST" style="max-width:400px;margin:40px auto;border:1px solid #ccc;padding:20px;border-radius:8px;">
    <h2>Sign Up</h2>
    <label>Name:</label><br>
    <input type="text" name="name" required style="width:100%;padding:8px;margin:5px 0;"><br>

    <label>Email:</label><br>
    <input type="email" name="email" required style="width:100%;padding:8px;margin:5px 0;"><br>

    <label>Password:</label><br>
    <input type="password" name="password" required style="width:100%;padding:8px;margin:5px 0;"><br>

    <button type="submit" style="background:#007bff;color:white;padding:10px 15px;border:none;border-radius:5px;">Sign Up</button>
    <p>Already have an account? <a href="login.php">Login</a></p>
</form>
