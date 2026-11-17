<?php

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

<<<<<<< HEAD
if (($_SESSION['role'] ?? '') !== 'admin') {
    set_message('Only admins can manage users.', 'danger');
=======
if (!is_super_admin()) {
    set_message('Only the super admin can edit users.', 'danger');
>>>>>>> 7c794f68c02dd4ad8dd351b3b3e2318287546479
    header('Location: ../dashboard/index.php');
    exit;
}

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT id, username, full_name, role FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    set_message('User not found.', 'danger');
    header('Location: index.php');
    exit;
}

$errors = [];
$username = $user['username']; $full_name = $user['full_name']; $role = $user['role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $role      = $_POST['role'] ?? 'librarian';
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($username === '')  $errors[] = 'Username is required.';
    if ($full_name === '') $errors[] = 'Full name is required.';
    if (!in_array($role, ['admin', 'librarian'], true)) $errors[] = 'Invalid role.';

    // Username unique, ignoring THIS user
    if ($username !== '') {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ? AND id <> ?');
        $stmt->execute([$username, $id]);
        if ($stmt->fetchColumn() > 0) $errors[] = 'That username is already taken.';
    }

    // Password is optional. Only validate it if they typed something.
    $changing_password = ($password !== '' || $password2 !== '');
    if ($changing_password) {
        if (strlen($password) < 6) $errors[] = 'New password must be at least 6 characters.';
        if ($password !== $password2) $errors[] = 'The two passwords do not match.';
    }

    // Safety: don't let the LAST admin be demoted (would lock everyone out)
    if ($user['role'] === 'admin' && $role !== 'admin') {
        $admin_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
        if ($admin_count <= 1) {
            $errors[] = 'Cannot change role: this is the only admin left.';
        }
    }

    if (count($errors) === 0) {
        if ($changing_password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                'UPDATE users SET username = ?, full_name = ?, role = ?, password = ? WHERE id = ?'
            );
            $stmt->execute([$username, $full_name, $role, $hash, $id]);
        } else {
            $stmt = $pdo->prepare(
                'UPDATE users SET username = ?, full_name = ?, role = ? WHERE id = ?'
            );
            $stmt->execute([$username, $full_name, $role, $id]);
        }

        set_message('User updated.');
        header('Location: index.php');
        exit;
    }
}

$page_title = 'Edit User';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <h1>Edit User</h1>
    <a class="btn btn-secondary" href="index.php">&larr; Back to list</a>
</div>

<?php if (count($errors) > 0): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="form-card">
    <form method="post" action="edit.php">
        <input type="hidden" name="id" value="<?= (int) $id ?>">

        <label for="username">Username *</label>
        <input type="text" id="username" name="username" value="<?= e($username) ?>" required>

        <label for="full_name">Full Name *</label>
        <input type="text" id="full_name" name="full_name" value="<?= e($full_name) ?>" required>

        <label for="role">Role *</label>
        <select id="role" name="role" required>
            <option value="librarian" <?= $role === 'librarian' ? 'selected' : '' ?>>librarian</option>
            <option value="admin"     <?= $role === 'admin'     ? 'selected' : '' ?>>admin</option>
        </select>

        <label for="password">New Password <span class="text-muted">(leave blank to keep current)</span></label>
        <input type="password" id="password" name="password">

        <label for="password2">Confirm New Password</label>
        <input type="password" id="password2" name="password2">

        <br><br>
        <button type="submit" class="btn">Update User</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
