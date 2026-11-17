<?php
require_once __DIR__ . '/../includes/functions.php';

// Whitelist of where we are allowed to go next (prevents open-redirect abuse)
$targets = [
    'dashboard' => '../dashboard/index.php',
    'login'     => 'login.php',
];

$key  = $_GET['next'] ?? 'login';
$dest = $targets[$key] ?? 'login.php';
$msg  = ($key === 'dashboard') ? 'Signing you in…' : 'Signing you out…';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading… | Library System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- fallback: if JavaScript is off, still move on after 2s -->
    <meta http-equiv="refresh" content="2;url=<?= e($dest) ?>">
</head>
<body class="splash-page">
    <div class="splash" data-splash-redirect="<?= e($dest) ?>">
        <img src="../assets/images/logo-standalone.png" alt="Library System" class="splash-logo">
        <div class="splash-spinner"></div>
        <p class="splash-text"><?= e($msg) ?></p>
    </div>

    <script src="../assets/js/app.js?v=<?= @filemtime(__DIR__ . '/../assets/js/app.js') ?>"></script>
</body>
</html>
