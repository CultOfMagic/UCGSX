<?php
session_start();
require 'db_connection.php'; // Assume this file contains database connection

$error = '';
$email = '';

// Remember me functionality
if (isset($_COOKIE['remember_email'])) {
    $email = htmlspecialchars($_COOKIE['remember_email']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        // Database query using prepared statements
        $stmt = $pdo->prepare("SELECT id, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            
            // Set remember me cookie (1 month)
            if ($remember) {
                setcookie('remember_email', $email, time() + 60*60*24*30, '/', '', true, true);
            }
            
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Church Community Login</title>
    <link rel="stylesheet" href="../css/signin.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="hero-section">
            <img src="../assets/img/BG.jpg" alt="Church Community Illustration" class="hero-image">
            <h2>Welcome to Our Church Community</h2>
            <p>Connect, share, and grow together in faith</p>
        </div>

        <div class="login-container">
            <img src="../assets/img/Logo.png" alt="Church Logo" class="logo">
            <h1 class="form-title">UCGS Member Login</h1>

            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" 
                           value="<?= htmlspecialchars($email) ?>" 
                           required
                           autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" 
                           required
                           autocomplete="current-password">
                    <span class="password-toggle" onclick="togglePassword()">👁️</span>
                </div>

                <div class="form-group">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" id="remember">
                        Remember my email
                    </label>
                </div>

                <button type="submit" class="btn">Sign In</button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
        }
    </script>
</body>
</html>