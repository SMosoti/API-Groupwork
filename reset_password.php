<?php
session_start();
require_once 'db_connect.php';
require_once 'mail.php';

$step = $_GET['step'] ?? 'request';
$message = '';

if($step === 'request' && $_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email']);
    $stmt = $conn->prepare("SELECT id,name FROM users WHERE email=:e");
    $stmt->execute([':e'=>$email]); $user = $stmt->fetch();
    if(!$user){ $message="Email not found."; }
    else {
        $otp = rand(100000,999999);
        $conn->prepare("UPDATE users SET otp_code=:otp WHERE id=:id")->execute([':otp'=>$otp,':id'=>$user['id']]);
        sendOTPMail($email, $user['name'], $otp);
        header("Location: reset_password.php?step=verify&e=".urlencode($email)); exit;
    }
}

if($step === 'verify' && $_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = $_POST['email']; $otp = $_POST['otp'];
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=:e AND otp_code=:otp");
    $stmt->execute([':e'=>$email,':otp'=>$otp]); $u = $stmt->fetch();
    if($u) { header("Location: reset_password.php?step=reset&e=".urlencode($email)); exit; } else $message = "Invalid OTP";
}

if($step === 'reset' && $_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = $_POST['email']; $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $conn->prepare("UPDATE users SET password=:p, otp_code=NULL WHERE email=:e")->execute([':p'=>$pass,':e'=>$email]);
    echo "<script>alert('Password reset. You can login.'); window.location='login.php';</script>"; exit;
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Reset Password</title></head><body style="font-family:Poppins">
<div style="max-width:420px;margin:40px auto;background:#fff;padding:20px;border-radius:8px">
<?php if($step==='request'): ?>
<h3>Request password reset</h3>
<?php if($message) echo "<div style='color:red'>$message</div>"; ?>
<form method="POST"><input name="email" type="email" required placeholder="Your email"><br><button>Send OTP</button></form>
<?php elseif($step==='verify'): ?>
<h3>Enter OTP</h3><form method="POST"><input type="hidden" name="email" value="<?=htmlspecialchars($_GET['e']??'')?>"><input name="otp" required placeholder="OTP"><br><button>Verify</button></form>
<?php else: ?>
<h3>Set New Password</h3><form method="POST"><input type="hidden" name="email" value="<?=htmlspecialchars($_GET['e']??'')?>"><input type="password" name="password" required placeholder="New password"><br><button>Reset</button></form>
<?php endif; ?>
</div></body></html>

