<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

// Get every author, sorted by name. No user input -> query() is fine.
$authors = $pdo->query(
    'SELECT id, name, country FROM authors ORDER BY name'
)->fetchAll();

$page_title = 'Authors';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <h1>Authors</h1>
    <a class="btn" href="create.php">+ Add Author</a>
</div>

<?php if (count($authors) === 0): ?>
    <p class="text-muted">No authors yet. Click “Add Author” to create one.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Country</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($authors as $row): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= e($row['name']) ?></td>
                    <td><?= e($row['country']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
