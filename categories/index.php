<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

// Get every category, newest id last. No user input -> query() is fine.
$categories = $pdo->query(
    'SELECT id, name, description FROM categories ORDER BY name'
)->fetchAll();

$page_title = 'Categories';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <h1>Categories</h1>
    <a class="btn" href="create.php">+ Add Category</a>
</div>

<?php if (count($categories) === 0): ?>
    <p class="text-muted">No categories yet. Click “Add Category” to create one.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <!-- e() escapes the text so it is safe to show as HTML -->
                    <td><?= e($row['name']) ?></td>
                    <td><?= e($row['description']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
