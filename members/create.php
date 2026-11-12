<?php

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$errors = [];
$member_number = ''; $full_name = ''; $email = '';
$phone = ''; $status = 'active'; $date_joined = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_number = trim($_POST['member_number'] ?? '');
    $full_name     = trim($_POST['full_name'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $phone         = trim($_POST['phone'] ?? '');
    $status        = $_POST['status'] ?? 'active';
    $date_joined   = $_POST['date_joined'] ?? date('Y-m-d');

    if ($member_number === '') $errors[] = 'Member number is required.';
    if ($full_name === '')     $errors[] = 'Full name is required.';
    if ($email === '')         $errors[] = 'Email is required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'That email looks invalid.';
    if (!in_array($status, ['active', 'suspended'], true)) $errors[] = 'Invalid status.';
    if ($date_joined === '')   $errors[] = 'Date joined is required.';

    if ($member_number !== '') {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM members WHERE member_number = ?');
        $stmt->execute([$member_number]);
        if ($stmt->fetchColumn() > 0) $errors[] = 'That member number is already used.';
    }
    if ($email !== '') {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM members WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) $errors[] = 'That email is already registered.';
    }

    if (count($errors) === 0) {
        $photo = save_member_photo($_FILES['photo'] ?? null, $photo_err);
        if ($photo_err !== '') {
            $errors[] = $photo_err;
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO members (member_number, full_name, email, phone, photo, status, date_joined)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$member_number, $full_name, $email, $phone, $photo, $status, $date_joined]);

            set_message('Member added.');
            header('Location: index.php');
            exit;
        }
    }
}

$page_title = 'Add Member';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <h1>Add Member</h1>
    <a class="btn btn-secondary" href="index.php">&larr; Back to list</a>
</div>

<?php if (count($errors) > 0): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="form-card">
    <form method="post" action="create.php" enctype="multipart/form-data">
        <label for="member_number">Member Number *</label>
        <input type="text" id="member_number" name="member_number" value="<?= e($member_number) ?>" required>

        <label for="full_name">Full Name *</label>
        <input type="text" id="full_name" name="full_name" value="<?= e($full_name) ?>" required>

        <label for="email">Email *</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" required>

        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone" value="<?= e($phone) ?>">

        <label for="photo">Photo <span class="text-muted">(optional &mdash; JPG, PNG, GIF or WEBP, max 2 MB)</span></label>
        <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/gif,image/webp">

        <label for="status">Status *</label>
        <select id="status" name="status" required>
            <option value="active"    <?= $status === 'active'    ? 'selected' : '' ?>>active</option>
            <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>suspended</option>
        </select>

        <label for="date_joined">Date Joined *</label>
        <input type="date" id="date_joined" name="date_joined" value="<?= e($date_joined) ?>" required>

        <br><br>
        <button type="submit" class="btn">Save Member</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
