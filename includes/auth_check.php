<?php
require_once __DIR__ . '/functions.php';

if (!isset($_SESSION['user_id'])) {
    set_message('Please log in first.', 'danger');
    header('Location: ../auth/login.php');
    exit;
}