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

<<<<<<< HEAD
        // Send OTP
        $mail->addAddress($email);
        $mail->Subject = "Verify Your Email - Weston Hotel";
        $mail->Body = "Hello $name! Your verification OTP is: $otp_code";
        if($mail->send()){
            $_SESSION['email'] = $email;
            header("Location: verify_otp.php");
=======
    try {
        // Check if user already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $check->execute([':email' => $email]);
        if ($check->fetch()) {
            echo "<div class='error-msg'>❌ Account already exists. <a href='login.php'>Login instead</a></div>";
>>>>>>> 60a6e252ca144b505ab40824e18cabeda9a7f0fe
            exit;
        } else {
            $error = "Could not send OTP email.";
        }
<<<<<<< HEAD
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
=======

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
        echo "<div class='error-msg'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Weston Hotel</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(to right, #d0eaff, #eaf6ff);
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        form {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 40px 30px;
            width: 100%;
            max-width: 420px;
        }

        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 25px;
            font-weight: 600;
        }

        label {
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0 16px 0;
            border: 1px solid #bcdcff;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
        }

        input:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0,123,255,0.3);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            border: none;
            color: white;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #005ecb;
        }

        p {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .error-msg, .success-msg {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 10px;
            margin: 10px auto;
            max-width: 420px;
            text-align: center;
        }

        .success-msg {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Create Account</h2>

        <label>Name</label><br>
        <input type="text" name="name" required><br>

        <label>Email</label><br>
        <input type="email" name="email" required><br>

        <label>Password</label><br>
        <input type="password" name="password" required><br>

        <button type="submit">Sign Up</button>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </form>
>>>>>>> 60a6e252ca144b505ab40824e18cabeda9a7f0fe
</body>
</html>
