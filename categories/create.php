<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$errors = [];
$name = '';
$description = '';

// Only process when the form was submitted (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    // Check the name is not already taken (name is UNIQUE in the table)
    if ($name !== '') {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM categories WHERE name = ?');
        $stmt->execute([$name]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'A category with that name already exists.';
        }
    }

    if (count($errors) === 0) {
        $stmt = $pdo->prepare(
            'INSERT INTO categories (name, description) VALUES (?, ?)'
        );
        $stmt->execute([$name, $description]);

        set_message('Category added.');
        header('Location: index.php');
        exit;
    }
}

$page_title = 'Add Category';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <h1>Add Category</h1>
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
        <!-- value refills the box with what was typed, kept safe by e() -->
        <input type="text" id="name" name="name" value="<?= e($name) ?>" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="3"><?= e($description) ?></textarea>

        <br><br>
        <button type="submit" class="btn">Save Category</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
