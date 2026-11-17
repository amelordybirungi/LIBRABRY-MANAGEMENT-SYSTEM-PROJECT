<?php
$DB_HOST = 'localhost';
$DB_NAME = 'library_db';
$DB_USER = 'root';
$DB_PASS = '';

$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";

// Options that make PDO behave sensibly and safely
$options = [
    // 1. Throw an error we can see, instead of failing silently
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // 2. Give us rows as simple name => value arrays by default
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // 3. Use REAL prepared statements (extra protection against SQL injection)
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // Log the real reason for us, but never show database internals to the user
    // (exposing them would leak details an attacker could use).
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(500);
    die('Sorry, the system is temporarily unavailable. Please try again later.');
}
