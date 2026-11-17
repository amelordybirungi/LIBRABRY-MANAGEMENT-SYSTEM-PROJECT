<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

// If already logged in, skip the form and go straight to the dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ../dashboard/index.php');
    exit;
}

$error = '';

// Only run the checking code when the form was SUBMITTED (a POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        // Find the user with a PREPARED STATEMENT (the ? keeps input out of the SQL)
        $stmt = $pdo->prepare('SELECT id, username, password, full_name, role
                               FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Compare the typed password with the stored hash
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);           // fresh session ID (security)

            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];

            set_message('Welcome, ' . $user['full_name'] . '!');
            header('Location: splash.php?next=dashboard');
            exit;
        } else {
            $error = 'Wrong username or password.';  // same message for both = safer
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Library System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-page">

<div class="auth-card">

    <div class="auth-visual"></div>

    <div class="auth-form-side">
        <div class="auth-brand">
            <img src="../assets/images/logo-standalone.png" alt="Library System logo" class="auth-logo">
            <h1>Login</h1>
        </div>

        
        <p class="auth-subtitle">Welcome back! Please sign in to continue.</p>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="login.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username"
                   placeholder="Enter your username" autofocus required>

            <label for="password">Password</label>
            <div class="password-field">
                <input type="password" id="password" name="password"
                       placeholder="Enter your password" required>
                <button type="button" class="password-toggle" aria-label="Show password"
                        onclick="togglePassword(this)">
                    <!-- eye (shown when password is hidden) -->
                    <svg class="icon-eye" width="20" height="20" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <!-- eye-off (shown when password is visible); hidden by default -->
                    <svg class="icon-eye-off" width="20" height="20" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         style="display:none">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                        <line x1="1" y1="1" x2="23" y2="23"/>
                    </svg>
                </button>
            </div>

            <button type="submit" class="btn btn-block" style="margin-top:20px;">Login</button>
        </form>
    </div>
</div>

<script src="../assets/js/app.js?v=<?= @filemtime(__DIR__ . '/../assets/js/app.js') ?>"></script>
</body>
</html>
