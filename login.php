<?php
session_start();
require_once 'db_connect.php';
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Generate OTP
        $otp = rand(100000, 999999);
        $expires_at = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        $insert = $conn->prepare("INSERT INTO otp_codes (user_id, code, expires_at, used) VALUES (:user_id, :code, :expires_at, FALSE)");
        $insert->execute([
            ':user_id' => $user['id'],
            ':code' => $otp,
            ':expires_at' => $expires_at
        ]);

        $_SESSION['temp_user_id'] = $user['id'];
        $_SESSION['temp_name'] = $user['name'];
        $_SESSION['temp_email'] = $user['email'];

        // Send OTP via email
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'sherly.mosoti@strathmore.edu';
            $mail->Password = 'oksi juad idbr ytoi';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('sherly.mosoti@strathmore.edu', 'Hotel Reservation');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your Login OTP';
            $mail->Body = "
                <h3>Hello {$user['name']},</h3>
                <p>Your one-time password (OTP) for login is:</p>
                <h2 style='color:#007bff;'>{$otp}</h2>
                <p>This code expires in 5 minutes.</p>
                <p>Thank you,<br><b>Riverside Residences Team</b></p>
            ";
            $mail->send();
        } catch (Exception $e) {
            echo "Email sending failed. Error: {$mail->ErrorInfo}";
        }

        header("Location: verify.php");
        exit;
    } else {
        $error = "❌ Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Hotel Login</title>
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(120deg, #d0e7ff, #e6f3ff);
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
    <h2>🏨 Hotel Login</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Enter your email" required><br>
        <input type="password" name="password" placeholder="Enter your password" required><br>
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
