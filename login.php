<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        echo "✅ Welcome, " . htmlspecialchars($user['name']) . "!<br>";
        echo "<a href='logout.php'>Logout</a>";
        // header("Location: dashboard.php");
    } else {
        echo "❌ Invalid email or password.<br>";
        echo "<a href='signup.php'>Create an account</a>";
    }
}
?>

<!-- HTML Login Form -->
<form method="POST" style="max-width:400px;margin:40px auto;border:1px solid #ccc;padding:20px;border-radius:8px;">
    <h2>Login</h2>
    <label>Email:</label><br>
    <input type="email" name="email" required style="width:100%;padding:8px;margin:5px 0;"><br>

    <label>Password:</label><br>
    <input type="password" name="password" required style="width:100%;padding:8px;margin:5px 0;"><br>

    <button type="submit" style="background:#007bff;color:white;padding:10px 15px;border:none;border-radius:5px;">Login</button>
    <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
</form>
