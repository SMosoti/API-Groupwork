<?php
$host = "localhost";        
$port = "5432";             
$dbname = "API_GROUPWORK";  
$user = "postgres";         
$password ="postgres"; 

try {
   
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>
