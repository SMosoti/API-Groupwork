<?php
// mpesa_callback.php
require_once 'db_connect.php';
date_default_timezone_set('Africa/Nairobi');

// Read raw JSON data from Safaricom
$callbackJSON = file_get_contents('php://input');

// Save raw data for debugging
file_put_contents('mpesa_logs.txt', $callbackJSON . PHP_EOL, FILE_APPEND);

// Decode JSON
$callbackData = json_decode($callbackJSON, true);

if (!$callbackData || !isset($callbackData['Body']['stkCallback'])) {
    http_response_code(400);
    echo json_encode(["ResultCode" => 1, "ResultDesc" => "Invalid callback received"]);
    exit;
}

$stkCallback = $callbackData['Body']['stkCallback'];
$resultCode  = $stkCallback['ResultCode'];
$resultDesc  = $stkCallback['ResultDesc'];

// If payment successful
if ($resultCode == 0) {
    $amount = $stkCallback['CallbackMetadata']['Item'][0]['Value'];
    $mpesaReceipt = $stkCallback['CallbackMetadata']['Item'][1]['Value'];
    $phone = $stkCallback['CallbackMetadata']['Item'][4]['Value'];
    $timestamp = date('Y-m-d H:i:s');

    // The AccountReference contains "Reservation-<id>"
    $accountRef = $stkCallback['CallbackMetadata']['Item'][6]['Value'] ?? '';
    $res_id = null;

    if (preg_match('/Reservation-(\d+)/', $accountRef, $matches)) {
        $res_id = $matches[1];
    }

    if ($res_id) {
        // Update reservation status
        $stmt = $conn->prepare("UPDATE reservations SET payment_status='Paid', mpesa_code=?, payment_date=? WHERE reservation_id=?");
        $stmt->execute([$mpesaReceipt, $timestamp, $res_id]);

        // Log confirmation
        file_put_contents('mpesa_logs.txt', "Reservation #$res_id marked as PAID\n", FILE_APPEND);
    }
}

// Respond to Safaricom
echo json_encode([
    "ResultCode" => 0,
    "ResultDesc" => "Callback processed successfully"
]);
?>
