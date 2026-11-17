<?php
// Only runs on POST so a plain link can't delete.
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

// A book that appears in any loan cannot be deleted (foreign key),
// so we check first and show a friendly message instead of a crash.
$stmt = $pdo->prepare('SELECT COUNT(*) FROM loans WHERE book_id = ?');
$stmt->execute([$id]);
$loan_count = $stmt->fetchColumn();

if ($loan_count > 0) {
    set_message('Cannot delete: this book has loan records.', 'danger');
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM books WHERE id = ?');
$stmt->execute([$id]);

set_message('Book deleted.');
header('Location: index.php');
exit;
