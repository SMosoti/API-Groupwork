<?php
// mpesa_config.php
date_default_timezone_set('Africa/Nairobi');

// Use variables (so pay_now.php using $consumerKey will work)
$consumerKey    = "xIShQrBO7jjra6N6HRZJ8Oc55N3Ewj22qxdgEN19oHLeqSZZ";
$consumerSecret = "QAXRPoPe86VPiqCBg2CfGuORjMo0pmlTMXjNoNOnZyOSg5hfKIyGgiy86KO4a0dp";

$BusinessShortCode = "174379";  // sandbox paybill
$Passkey           = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";

// Your public tunnel URL from localhost.run (must be https)
define('CALLBACK_URL', 'https://f6e4e7f0b02c03.lhr.life/mpesa_callback.php');


// Environment base URLs
$mpesa_base = 'https://sandbox.safaricom.co.ke';
$mpesa_token_url = $mpesa_base . '/oauth/v1/generate?grant_type=client_credentials';
$mpesa_stk_url   = $mpesa_base . '/mpesa/stkpush/v1/processrequest';
