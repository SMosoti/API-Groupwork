<?php
require_once 'mpesa_config.php';
$credentials = base64_encode($consumerKey . ':' . $consumerSecret);
$ch = curl_init($mpesa_token_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
$info = curl_getinfo($ch);
$err = curl_error($ch);
curl_close($ch);
echo "HTTP code: " . $info['http_code'] . "\n";
echo "Curl error: " . $err . "\n";
echo "Response:\n";
echo $res;
