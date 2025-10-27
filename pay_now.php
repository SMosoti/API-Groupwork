<?php
require_once 'mpesa_config.php';

// Display the form if the user hasn’t submitted yet
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    if (!isset($_GET['reservation_id'])) {
        die("Reservation ID is missing.");
    }
    $reservation_id = $_GET['reservation_id'];
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Pay Now</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f9f9f9;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 100vh;
            }
            form {
                background: #fff;
                padding: 20px;
                border-radius: 12px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                text-align: center;
            }
            input[type="number"], button {
                padding: 10px;
                margin: 10px;
                width: 250px;
                border-radius: 8px;
                border: 1px solid #ccc;
            }
            button {
                background: #28a745;
                color: white;
                border: none;
                cursor: pointer;
            }
            button:hover {
                background: #218838;
            }
        </style>
    </head>
    <body>
        <form method="POST">
            <h3>Confirm Payment</h3>
            <p>Reservation ID: <strong><?php echo htmlspecialchars($reservation_id); ?></strong></p>
            <label>Enter your M-Pesa number:</label><br>
            <input type="number" name="phone" placeholder="2547XXXXXXXX" required><br>
            <button type="submit">Pay Now</button>
        </form>
    </body>
    </html>

    <?php
    exit;
}

// ✅ Once form is submitted, process the payment
$phone = $_POST['phone'];
$reservation_id = $_GET['reservation_id'] ?? 0;

// --- Step 1: Get access token ---
$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json; charset=utf8']);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_USERPWD, CONSUMER_KEY . ':' . CONSUMER_SECRET);
$result = curl_exec($curl);
$response = json_decode($result);
$access_token = $response->access_token ?? null;

if (!$access_token) {
    die("Failed to generate access token. Try again.");
}

// --- Step 2: STK Push ---
$timestamp = date("YmdHis");
$password = base64_encode(BUSINESS_SHORT_CODE . PASSKEY . $timestamp);
$amount = 1; // ✅ Replace with real total amount from your booking system

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Content-Type:application/json',
    'Authorization:Bearer ' . $access_token
]);
$curl_post_data = [
    'BusinessShortCode' => BUSINESS_SHORT_CODE,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $amount,
    'PartyA' => $phone,
    'PartyB' => BUSINESS_SHORT_CODE,
    'PhoneNumber' => $phone,
    'CallBackURL' => CALLBACK_URL,
    'AccountReference' => "Reservation_$reservation_id",
    'TransactionDesc' => 'Room Booking Payment'
];
$data_string = json_encode($curl_post_data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
$curl_response = curl_exec($curl);

$response_data = json_decode($curl_response, true);

if (isset($response_data['ResponseCode']) && $response_data['ResponseCode'] == "0") {
    echo "<h3>✅ Payment prompt sent to your phone. Please complete the transaction.</h3>";
} else {
    echo "<h3>❌ Payment failed. " . htmlspecialchars($response_data['errorMessage'] ?? 'Please try again.') . "</h3>";
}

?>
