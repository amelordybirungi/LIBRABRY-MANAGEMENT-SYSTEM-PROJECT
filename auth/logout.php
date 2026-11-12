<?php
require_once __DIR__ . '/../includes/functions.php';

$_SESSION = [];
session_destroy();

// Start a brand-new session just to carry one goodbye message
session_start();
set_message('You have been logged out.');

header('Location: splash.php?next=login');
exit;
