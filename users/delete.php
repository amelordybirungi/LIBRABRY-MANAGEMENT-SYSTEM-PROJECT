<?php
// Only runs on POST so a plain link can't delete an account.
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

// Only the super admin (username 'admin') may delete users.
if (!is_super_admin()) {
    set_message('Only the super admin can delete users.', 'danger');
    header('Location: ../dashboard/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT id, username, role FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    set_message('User not found.', 'danger');
    header('Location: index.php');
    exit;
}

// The super admin account itself can never be removed.
if ($user['username'] === 'admin') {
    set_message('The super admin account cannot be deleted.', 'danger');
    header('Location: index.php');
    exit;
}

// You cannot delete the account you are currently logged in as.
if ((int) $user['id'] === (int) ($_SESSION['user_id'] ?? 0)) {
    set_message('You cannot delete your own account.', 'danger');
    header('Location: index.php');
    exit;
}

// Never remove the last remaining admin (would lock everyone out).
if ($user['role'] === 'admin') {
    $admin_count = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    if ($admin_count <= 1) {
        set_message('Cannot delete: this is the only admin left.', 'danger');
        header('Location: index.php');
        exit;
    }
}

$stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
$stmt->execute([$id]);

set_message('User deleted.');
header('Location: index.php');
exit;
