<?php

// Marking a loan returned does two things together (a transaction):
// set the loan's date_returned, and add 1 back to the book's available_copies.

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM loans WHERE id = ?');
$stmt->execute([$id]);
$loan = $stmt->fetch();

if (!$loan) {
    set_message('Loan not found.', 'danger');
    header('Location: index.php');
    exit;
}

// Already returned? Do nothing (avoids adding a copy twice).
if ($loan['date_returned'] !== null) {
    set_message('That loan was already returned.', 'danger');
    header('Location: index.php');
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('UPDATE loans SET date_returned = ? WHERE id = ?');
    $stmt->execute([date('Y-m-d'), $id]);

    $stmt = $pdo->prepare(
        'UPDATE books SET available_copies = available_copies + 1 WHERE id = ?'
    );
    $stmt->execute([$loan['book_id']]);

    $pdo->commit();

    set_message('Book returned.');
} catch (Exception $ex) {
    $pdo->rollBack();
    set_message('Could not return the book. Please try again.', 'danger');
}

header('Location: index.php');
exit;
