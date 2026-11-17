<?php

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

if (($_SESSION['role'] ?? '') !== 'admin') {
    set_message('Only admins can manage users.', 'danger');
    header('Location: ../dashboard/index.php');
    exit;
}

// We never SELECT the password column — no need to read it.
$users = $pdo->query(
    'SELECT id, username, full_name, role, created_at
     FROM users ORDER BY username'
)->fetchAll();

$page_title = 'Users';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <h1>Users</h1>
    <a class="btn" href="create.php">+ Add User</a>
</div>

<?php if (count($users) === 0): ?>
    <p class="text-muted">No users yet. Click “Add User” to create one.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Full Name</th>
                <th>Role</th>
                <th>Created</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $row): ?>
                <tr>
                    <td><?= e($row['username']) ?></td>
                    <td><?= e($row['full_name']) ?></td>
                    <td>
                        <span class="badge badge-<?= $row['role'] === 'admin' ? 'active' : 'suspended' ?>">
                            <?= e($row['role']) ?>
                        </span>
                    </td>
                    <td><?= e($row['created_at']) ?></td>
                    <td>
                        <a class="btn btn-small" href="edit.php?id=<?= (int) $row['id'] ?>">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
