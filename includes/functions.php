<?php
// Start the session (PHP's way to "remember" who is logged in between pages)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// e() = make text safe before printing it in HTML
function e($text)
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

// Save a message to show on the NEXT page ('success' = green, 'danger' = red)
function set_message($text, $type = 'success')
{
    $_SESSION['message'] = ['text' => $text, 'type' => $type];
}

// Print the saved message once, then forget it
function show_message()
{
    if (isset($_SESSION['message'])) {
        $msg = $_SESSION['message'];
        echo '<div class="alert alert-' . e($msg['type']) . '">' . e($msg['text']) . '</div>';
        unset($_SESSION['message']);
    }
}