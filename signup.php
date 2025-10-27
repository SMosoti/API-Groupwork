<?php
session_start();
require 'db_connect.php';
require 'mail.php'; // PHPMailer setup

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=:email");
    $stmt->execute([':email'=>$email]);
    if($stmt->rowCount()>0){
        $error = "Email already registered.";
    } else {
        $otp_code = rand(100000,999999); // 6-digit OTP
        $stmt = $conn->prepare("INSERT INTO users (name,email,password,role,otp_verified,otp_code) VALUES (:name,:email,:password,'client',0,:otp_code)");
        $stmt->execute([':name'=>$name, ':email'=>$email, ':password'=>$password, ':otp_code'=>$otp_code]);

        // Send OTP
        $mail->addAddress($email);
        $mail->Subject = "Verify Your Email - Weston Hotel";
        $mail->Body = "Hello $name! Your verification OTP is: $otp_code";
        if($mail->send()){
            $_SESSION['email'] = $email;
            header("Location: verify_otp.php");
            exit;
        } else {
            $error = "Could not send OTP email.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Sign Up | Weston Hotel</title>
<style>
body { font-family:'Poppins',sans-serif; background:#f5f8ff; display:flex; justify-content:center; align-items:center; height:100vh; }
form { background:white; padding:30px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.1); width:300px; }
input, button { width:100%; padding:10px; margin:8px 0; border-radius:5px; border:1px solid #ccc; }
button { background:#007bff; color:white; border:none; cursor:pointer; }
button:hover { background:#0056b3; }
.error { color:red; font-size:14px; }
</style>
</head>
<body>
<form method="POST">
<h3>Sign Up</h3>
<input type="text" name="name" placeholder="Full Name" required>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
<button type="submit">Sign Up</button>
<p>Already have an account? <a href="login.php">Login</a></p>
</form>
</body>
</html>
