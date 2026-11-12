<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$errors = [];
$name = '';
$country = '';

// Only process when the form was submitted (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name    = trim($_POST['name'] ?? '');
    $country = trim($_POST['country'] ?? '');

    // Validate (name is required; country may be empty)
    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if (count($errors) === 0) {
        $stmt = $pdo->prepare(
            'INSERT INTO authors (name, country) VALUES (?, ?)'
        );
        $stmt->execute([$name, $country]);

        set_message('Author added.');
        header('Location: index.php');
        exit;
    }
}

$page_title = 'Add Author';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <h1>Add Author</h1>
    <a class="btn btn-secondary" href="index.php">&larr; Back to list</a>
</div>

<?php if (count($errors) > 0): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $err): ?>
            <div><?= e($err) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="form-card">
    <form method="post" action="create.php">
        <label for="name">Name *</label>
        <input type="text" id="name" name="name" value="<?= e($name) ?>" required>

        <label for="country">Country</label>
        <input type="text" id="country" name="country" value="<?= e($country) ?>">

        <br><br>
        <button type="submit" class="btn">Save Author</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
