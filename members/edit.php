<?php

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM members WHERE id = ?');
$stmt->execute([$id]);
$member = $stmt->fetch();

if (!$member) {
    set_message('Member not found.', 'danger');
    header('Location: index.php');
    exit;
}

$errors = [];
$member_number = $member['member_number']; $full_name = $member['full_name'];
$email = $member['email']; $phone = $member['phone'];
$status = $member['status']; $date_joined = $member['date_joined'];
$photo = $member['photo'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_number = trim($_POST['member_number'] ?? '');
    $full_name     = trim($_POST['full_name'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $phone         = trim($_POST['phone'] ?? '');
    $status        = $_POST['status'] ?? 'active';
    $date_joined   = $_POST['date_joined'] ?? '';

    if ($member_number === '') $errors[] = 'Member number is required.';
    if ($full_name === '')     $errors[] = 'Full name is required.';
    if ($email === '')         $errors[] = 'Email is required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'That email looks invalid.';
    if (!in_array($status, ['active', 'suspended'], true)) $errors[] = 'Invalid status.';
    if ($date_joined === '')   $errors[] = 'Date joined is required.';

    // unique member_number, ignoring this member
    if ($member_number !== '') {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM members WHERE member_number = ? AND id <> ?');
        $stmt->execute([$member_number, $id]);
        if ($stmt->fetchColumn() > 0) $errors[] = 'That member number is already used.';
    }
    // unique email, ignoring this member
    if ($email !== '') {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM members WHERE email = ? AND id <> ?');
        $stmt->execute([$email, $id]);
        if ($stmt->fetchColumn() > 0) $errors[] = 'That email is already registered.';
    }

    if (count($errors) === 0) {
        $new_photo = $member['photo'];
        $uploaded  = save_member_photo($_FILES['photo'] ?? null, $photo_err);

        if ($photo_err !== '') {
            $errors[] = $photo_err;
        } else {
            if ($uploaded !== null) {
                delete_member_photo($member['photo']);
                $new_photo = $uploaded;
            } elseif (isset($_POST['remove_photo'])) {
                delete_member_photo($member['photo']);
                $new_photo = null;
            }

            $stmt = $pdo->prepare(
                'UPDATE members
                 SET member_number = ?, full_name = ?, email = ?, phone = ?, photo = ?, status = ?, date_joined = ?
                 WHERE id = ?'
            );
            $stmt->execute([$member_number, $full_name, $email, $phone, $new_photo, $status, $date_joined, $id]);

            set_message('Member updated.');
            header('Location: index.php');
            exit;
        }
    }
}

$page_title = 'Edit Member';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <h1>Edit Member</h1>
    <a class="btn btn-secondary" href="index.php">&larr; Back to list</a>
</div>

<?php if (count($errors) > 0): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="form-card">
    <form method="post" action="edit.php" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= (int) $id ?>">

        <label for="member_number">Member Number *</label>
        <input type="text" id="member_number" name="member_number" value="<?= e($member_number) ?>" required>

        <label for="full_name">Full Name *</label>
        <input type="text" id="full_name" name="full_name" value="<?= e($full_name) ?>" required>

        <label for="email">Email *</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" required>

        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone" value="<?= e($phone) ?>">

        <label>Current Photo</label>
        <div class="photo-edit">
            <?php member_avatar($photo, $full_name, 72); ?>
            <div class="photo-edit-controls">
                <label for="photo" class="photo-edit-label">
                    <?= $photo ? 'Replace photo' : 'Add a photo' ?>
                    <span class="text-muted">(JPG, PNG, GIF or WEBP, max 2 MB)</span>
                </label>
                <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/gif,image/webp">
                <?php if ($photo): ?>
                    <label class="photo-remove">
                        <input type="checkbox" name="remove_photo" value="1"> Remove current photo
                    </label>
                <?php endif; ?>
            </div>
        </div>

        <label for="status">Status *</label>
        <select id="status" name="status" required>
            <option value="active"    <?= $status === 'active'    ? 'selected' : '' ?>>active</option>
            <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>suspended</option>
        </select>

        <label for="date_joined">Date Joined *</label>
        <input type="date" id="date_joined" name="date_joined" value="<?= e($date_joined) ?>" required>

        <br><br>
        <button type="submit" class="btn">Update Member</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
