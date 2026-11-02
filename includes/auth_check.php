<?php
require_once __DIR__ . '/functions.php';   // __DIR__ = the folder this file is in

// No user_id in the session = nobody logged in -> go to login
if (!isset($_SESSION['user_id'])) {
    set_message('Please log in first.', 'danger');
    header('Location: ../auth/login.php');   // redirect the browser
    exit;                                     // STOP, show nothing else
}