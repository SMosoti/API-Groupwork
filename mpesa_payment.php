<?php
// mpesa_payment.php
session_start();
require_once 'db_connect.php';
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
$uid = $_SESSION['user_id'];
// reservation id must be passed via GET or POST
$resId = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['reservation_id']) ? intval($_POST['reservation_id']) : 0);
if(!$resId) { die("Reservation id missing."); }

// fetch reservation (ensure ownership if client)
$stmt = $conn->prepare("SELECT r.*, u.email,u.name FROM reservations r JOIN users u ON r.user_id = u.id WHERE r.reservation_id = :id");
$stmt->execute([':id'=>$resId]);
$res = $stmt->fetch();
if(!$res) die("Reservation not found.");
if($_SESSION['role']!=='admin' && intval($res['user_id'])!==intval($uid)){ die("Access denied."); }

if($_SERVER['REQUEST_METHOD']==='POST'){
    $phone = trim($_POST['phone_number']);
    $pin = trim($_POST['mpesa_pin']);
    // validate phone
    if(!preg_match('/^254\d{9}$/',$phone)){ $error="Phone must start with 254 and be 12 digits."; }
    else {
        // simulate transaction code
        $txn = 'MPESA'.strtoupper(substr(md5(time().$phone),0,8));
        // insert into mpesa_logs
        $stmt = $conn->prepare("INSERT INTO mpesa_logs (phone, amount, transaction_code) VALUES (:phone,:amount,:txn)");
        $stmt->execute([':phone'=>$phone,':amount'=>$res['total_price'],':txn'=>$txn]);

        // insert into payments
        $stmt = $conn->prepare("INSERT INTO payments (reservation_id, phone_number, amount, mpesa_pin, status) VALUES (:rid,:phone,:amt,:pin,'success')");
        $stmt->execute([':rid'=>$resId,':phone'=>$phone,':amt'=>$res['total_price'],':pin'=>$pin]);

        // update reservation payment_status & transaction_code
        $stmt = $conn->prepare("UPDATE reservations SET payment_status='paid', transaction_code=:txn WHERE reservation_id=:id");
        $stmt->execute([':txn'=>$txn,':id'=>$resId]);

        // respond success
        echo "<!doctype html><html><head><meta charset='utf-8'><title>Payment Success</title>";
        echo "<style>body{font-family:Inter,Arial;background:#f0f6ff;padding:60px;text-align:center} .box{background:#fff;display:inline-block;padding:28px;border-radius:12px;box-shadow:0 8px 24px rgba(13,110,253,0.08)}</style></head><body>";
        echo "<div class='box'><h2 style='color:#0d6efd'>Payment Successful</h2>";
        echo "<p>Transaction: <strong>".htmlspecialchars($txn)."</strong></p>";
        echo "<p>Amount: KES ".number_format($res['total_price'],2)."</p>";
        echo "<p><a style='display:inline-block;margin-top:12px;padding:10px 16px;background:#0d6efd;color:#fff;border-radius:8px;text-decoration:none' href='view_reservation.php'>Back to Reservations</a></p></div></body></html>";
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8"><title>M-PESA Payment</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
 body{font-family:Inter,Arial;background:#f3f7fb;margin:0}
 header{background:linear-gradient(90deg,#0d6efd,#0b5ed7);color:#fff;padding:14px}
 .wrap{max-width:480px;margin:40px auto;padding:12px}
 .card{background:#fff;padding:22px;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.06)}
 input{width:100%;padding:10px;margin:8px 0;border:1px solid #dfeeff;border-radius:8px}
 .btn{background:#0d6efd;color:#fff;padding:10px 14px;border-radius:8px;border:none;cursor:pointer}
 .error{color:#dc3545}
</style>
</head>
<body>
<header><div style="max-width:1100px;margin:0 auto"><h2 style="margin:0">M-PESA Payment</h2></div></header>
<div class="wrap">
  <div class="card">
    <h3 style="margin-top:0">Reservation #<?=htmlspecialchars($res['reservation_id'])?></h3>
    <p><strong>Client:</strong> <?=htmlspecialchars($res['name'])?> (<?=htmlspecialchars($res['email'])?>)</p>
    <p><strong>Amount:</strong> KES <?=number_format($res['total_price'],2)?></p>
    <?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="post">
      <input type="hidden" name="reservation_id" value="<?=$resId?>">
      <label>Phone (254...)</label>
      <input name="phone_number" placeholder="2547XXXXXXXX" required>
      <label>M-PESA PIN</label>
      <input name="mpesa_pin" type="password" placeholder="****" required>
      <div style="text-align:right;margin-top:10px"><button class="btn" type="submit">Pay Now</button></div>
    </form>
  </div>
</div>
</body>
</html>
