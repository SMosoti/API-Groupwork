<?php
session_start();
require_once 'db_connect.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='admin'){ header('Location: login.php'); exit; }
$id = intval($_GET['id'] ?? 0);
if($id){
    $conn->prepare("DELETE FROM rooms WHERE room_id=:id")->execute([':id'=>$id]);
}
header('Location: admin_dashboard.php'); exit;
