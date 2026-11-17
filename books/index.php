<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$books = $pdo->query(
    'SELECT b.id, b.isbn, b.title, b.year_published,
            b.total_copies, b.available_copies,
            a.name AS author_name,
            c.name AS category_name
     FROM books b
     JOIN authors    a ON a.id = b.author_id
     JOIN categories c ON c.id = b.category_id
     ORDER BY b.title'
)->fetchAll();

$page_title = 'Books';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <h1>Books</h1>
    <a class="btn" href="create.php">+ Add Book</a>
</div>

<?php if (count($books) === 0): ?>
    <p class="text-muted">No books yet. Click “Add Book” to create one.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
<<<<<<< HEAD
                <th>Title</th>
=======
                <th>Title name</th>
>>>>>>> 7c794f68c02dd4ad8dd351b3b3e2318287546479
                <th>Author</th>
                <th>Category</th>
                <th>Year</th>
                <th>Available</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($books as $row): ?>
                <tr>
                    <td><?= e($row['title']) ?></td>
                    <td><?= e($row['author_name']) ?></td>
                    <td><?= e($row['category_name']) ?></td>
                    <td><?= e($row['year_published']) ?></td>
                    <td><?= (int) $row['available_copies'] ?> / <?= (int) $row['total_copies'] ?></td>
                    <td>
                        <a class="btn btn-small" href="edit.php?id=<?= (int) $row['id'] ?>">Edit</a>
                        <!-- Delete is a small POST form so a click (not a link crawl) is needed -->
                        <form method="post" action="delete.php" style="display:inline"
                              onsubmit="return confirm('Delete this book?');">
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
