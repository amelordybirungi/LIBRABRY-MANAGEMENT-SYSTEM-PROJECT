<?php
require_once __DIR__ . '/functions.php';

$page_title = $page_title ?? 'Library System';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> | Library System</title>
    <?php // ?v=<file time> forces browsers to reload the CSS whenever it actually changes ?>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= @filemtime(__DIR__ . '/../assets/css/style.css') ?>">
</head>
<body>

<?php
// Show the navigation bar ONLY when someone is logged in.
// (The login page has no user yet, so it shows no menu.)
if (isset($_SESSION['user_id'])) {
    require __DIR__ . '/navbar.php';
}
?>

<main class="container">
    <?php show_message(); ?>
