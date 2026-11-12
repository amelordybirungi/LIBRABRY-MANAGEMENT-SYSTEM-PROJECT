<?php

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

// Does this member appear in any loan?
$stmt = $pdo->prepare('SELECT COUNT(*) FROM loans WHERE member_id = ?');
$stmt->execute([$id]);
if ($stmt->fetchColumn() > 0) {
    set_message('Cannot delete: this member has loan records.', 'danger');
    header('Location: index.php');
    exit;
}

// Grab the photo file name first, so we can delete it after the row is gone.
$stmt = $pdo->prepare('SELECT photo FROM members WHERE id = ?');
$stmt->execute([$id]);
$old_photo = $stmt->fetchColumn();

$stmt = $pdo->prepare('DELETE FROM members WHERE id = ?');
$stmt->execute([$id]);

delete_member_photo($old_photo ?: null);

set_message('Member deleted.');
header('Location: index.php');
exit;
