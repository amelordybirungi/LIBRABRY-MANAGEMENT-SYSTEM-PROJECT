<?php

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$status = $_GET['status'] ?? '';

$where  = [];
$params = [];

if ($status === 'active' || $status === 'suspended') {
    $where[]  = 'status = ?';
    $params[] = $status;
}

$sql = 'SELECT id, member_number, full_name, email, phone, photo, status, date_joined FROM members';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY full_name';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$members = $stmt->fetchAll();

$filtering = ($status !== '');

$page_title = 'Members';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <h1>Members</h1>
    <a class="btn" href="create.php">+ Add Member</a>
</div>

<form class="filter-bar" method="get" action="index.php">
    <label>
        Status
        <select name="status" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="active"    <?= $status === 'active'    ? 'selected' : '' ?>>Active</option>
            <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Suspended</option>
        </select>
    </label>
    <?php if ($filtering): ?>
        <a class="btn btn-small btn-secondary" href="index.php">Clear</a>
    <?php endif; ?>
</form>

<?php if (count($members) === 0): ?>
    <p class="text-muted">
        <?= $filtering ? 'No members match your filter.' : 'No members yet. Click “Add Member” to create one.' ?>
    </p>
<?php else: ?>
    <p class="text-muted" style="margin-bottom:12px;">
<<<<<<< HEAD
         <?= count($members) ?> member<?= count($members) === 1 ? '' : 's' ?><?= $filtering ? ' (filtered)' : '' ?>.
=======
          <?= count($members) ?> member<?= count($members) === 1 ? '' : 's' ?><?= $filtering ? ' (filtered)' : '' ?>.
>>>>>>> 7c794f68c02dd4ad8dd351b3b3e2318287546479
    </p>
    <table>
        <thead>
            <tr>
                <th>Photo</th>
<<<<<<< HEAD
                <th>Member #</th>
=======
                <th>Member-ID</th>
>>>>>>> 7c794f68c02dd4ad8dd351b3b3e2318287546479
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($members as $row): ?>
                <tr>
                    <td><?php member_avatar($row['photo'] ?? null, $row['full_name'], 40); ?></td>
                    <td><?= e($row['member_number']) ?></td>
                    <td><?= e($row['full_name']) ?></td>
                    <td><?= e($row['email']) ?></td>
                    <td><?= e($row['phone']) ?></td>
                    <td>
                        <span class="badge badge-<?= $row['status'] === 'active' ? 'active' : 'suspended' ?>">
                            <?= e($row['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a class="btn btn-small" href="edit.php?id=<?= (int) $row['id'] ?>">Edit</a>
                        <form method="post" action="delete.php" style="display:inline"
                              onsubmit="return confirm('Delete this member?');">
                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                            <button type="submit" class="btn btn-small btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
