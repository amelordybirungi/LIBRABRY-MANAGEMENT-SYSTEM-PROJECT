<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$q = trim($_GET['q'] ?? '');

// These arrays stay empty unless there is something to search for.
$books = $members = $authors = $categories = [];
$total = 0;

if ($q !== '') {
    // The % signs make it a "contains" search, e.g. %har% matches "Charlotte".
    $like = '%' . $q . '%';

    $stmt = $pdo->prepare(
        'SELECT b.id, b.title, b.isbn,
                a.name AS author_name,
                c.name AS category_name
         FROM books b
         JOIN authors    a ON a.id = b.author_id
         JOIN categories c ON c.id = b.category_id
         WHERE b.title LIKE ? OR b.isbn LIKE ? OR a.name LIKE ? OR c.name LIKE ?
         ORDER BY b.title'
    );
    $stmt->execute([$like, $like, $like, $like]);
    $books = $stmt->fetchAll();

    $stmt = $pdo->prepare(
        'SELECT id, member_number, full_name, email, phone, status
         FROM members
         WHERE full_name LIKE ? OR member_number LIKE ? OR email LIKE ? OR phone LIKE ?
         ORDER BY full_name'
    );
    $stmt->execute([$like, $like, $like, $like]);
    $members = $stmt->fetchAll();

    $stmt = $pdo->prepare(
        'SELECT id, name, country FROM authors
         WHERE name LIKE ? OR country LIKE ? ORDER BY name'
    );
    $stmt->execute([$like, $like]);
    $authors = $stmt->fetchAll();

    $stmt = $pdo->prepare(
        'SELECT id, name, description FROM categories
         WHERE name LIKE ? OR description LIKE ? ORDER BY name'
    );
    $stmt->execute([$like, $like]);
    $categories = $stmt->fetchAll();

    // How many results in total (used for the "nothing found" message).
    $total = count($books) + count($members) + count($authors) + count($categories);
}

$page_title = 'Search';
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Search</h1>

<!-- The search box also lives in the navbar, but we repeat it here so the
     page is usable on its own and keeps whatever was typed. -->
<form class="search-page-form" method="get" action="index.php">
    <input type="text" name="q" value="<?= e($q) ?>"
           placeholder="Search books, members, authors, categories..." autofocus>
    <button type="submit" class="btn">Search</button>
</form>

<?php if ($q === ''): ?>
    <p class="text-muted">Type something above to search across the whole system.</p>

<?php elseif ($total === 0): ?>
    <p class="text-muted">No results found for &ldquo;<?= e($q) ?>&rdquo;.</p>

<?php else: ?>
    <p class="text-muted" style="margin-bottom:18px;">
        Found <?= (int) $total ?> result<?= $total === 1 ? '' : 's' ?> for &ldquo;<?= e($q) ?>&rdquo;.
    </p>

    <?php if (count($books) > 0): ?>
        <h2>Books (<?= count($books) ?>)</h2>
        <table style="margin-bottom:26px;">
            <thead>
                <tr><th>Title</th><th>Author</th><th>Category</th><th>ISBN</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach ($books as $row): ?>
                    <tr>
                        <td><?= e($row['title']) ?></td>
                        <td><?= e($row['author_name']) ?></td>
                        <td><?= e($row['category_name']) ?></td>
                        <td><?= e($row['isbn']) ?></td>
                        <td><a class="btn btn-small" href="../books/edit.php?id=<?= (int) $row['id'] ?>">Open</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if (count($members) > 0): ?>
        <h2>Members (<?= count($members) ?>)</h2>
        <table style="margin-bottom:26px;">
            <thead>
                <tr><th>Member #</th><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach ($members as $row): ?>
                    <tr>
                        <td><?= e($row['member_number']) ?></td>
                        <td><?= e($row['full_name']) ?></td>
                        <td><?= e($row['email']) ?></td>
                        <td><?= e($row['phone']) ?></td>
                        <td>
                            <span class="badge badge-<?= $row['status'] === 'active' ? 'active' : 'suspended' ?>">
                                <?= e($row['status']) ?>
                            </span>
                        </td>
                        <td><a class="btn btn-small" href="../members/edit.php?id=<?= (int) $row['id'] ?>">Open</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if (count($authors) > 0): ?>
        <h2>Authors (<?= count($authors) ?>)</h2>
        <table style="margin-bottom:26px;">
            <thead>
                <tr><th>Name</th><th>Country</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach ($authors as $row): ?>
                    <tr>
                        <td><?= e($row['name']) ?></td>
                        <td><?= e($row['country']) ?></td>
                        <td><a class="btn btn-small" href="../authors/index.php">View list</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if (count($categories) > 0): ?>
        <h2>Categories (<?= count($categories) ?>)</h2>
        <table style="margin-bottom:26px;">
            <thead>
                <tr><th>Name</th><th>Description</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $row): ?>
                    <tr>
                        <td><?= e($row['name']) ?></td>
                        <td><?= e($row['description']) ?></td>
                        <td><a class="btn btn-small" href="../categories/index.php">View list</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
