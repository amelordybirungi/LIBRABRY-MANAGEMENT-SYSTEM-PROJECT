<?php

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

if (!is_super_admin()) {
    set_message('Only the super admin can add users.', 'danger');
    header('Location: ../dashboard/index.php');
    exit;
}

$errors = [];
$username = ''; $full_name = ''; $role = 'librarian';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $role      = $_POST['role'] ?? 'librarian';
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($username === '')  $errors[] = 'Username is required.';
    if ($full_name === '') $errors[] = 'Full name is required.';
    if (!in_array($role, ['admin', 'librarian'], true)) $errors[] = 'Invalid role.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $password2) $errors[] = 'The two passwords do not match.';

    if ($username !== '') {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) $errors[] = 'That username is already taken.';
    }

    if (count($errors) === 0) {
        // NEVER store the plain password — store a one-way hash
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare(
            'INSERT INTO users (username, password, full_name, role)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$username, $hash, $full_name, $role]);

        set_message('User added.');
        header('Location: index.php');
        exit;
    }
}

$page_title = 'Add User';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <h1>Add User</h1>
    <a class="btn btn-secondary" href="index.php">&larr; Back to list</a>
</div>

<?php if (count($errors) > 0): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="form-card">
    <form method="post" action="create.php">
        <label for="username">Username *</label>
        <input type="text" id="username" name="username" value="<?= e($username) ?>" required>

        <label for="full_name">Full Name *</label>
        <input type="text" id="full_name" name="full_name" value="<?= e($full_name) ?>" required>

        <label for="role">Role *</label>
        <select id="role" name="role" required>
            <option value="librarian" <?= $role === 'librarian' ? 'selected' : '' ?>>librarian</option>
            <option value="admin"     <?= $role === 'admin'     ? 'selected' : '' ?>>admin</option>
        </select>

        <label for="password">Password * <span class="text-muted">(min 6 characters)</span></label>
        <input type="password" id="password" name="password" required>

        <label for="password2">Confirm Password *</label>
        <input type="password" id="password2" name="password2" required>

        <br><br>
        <button type="submit" class="btn">Save User</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
