<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM books WHERE id = ?');
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    set_message('Book not found.', 'danger');
    header('Location: index.php');
    exit;
}

$authors    = $pdo->query('SELECT id, name FROM authors ORDER BY name')->fetchAll();
$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();

// How many copies are out right now (can't reduce total below this)
$copies_out = (int) $book['total_copies'] - (int) $book['available_copies'];

$errors = [];
$isbn = $book['isbn']; $title = $book['title'];
$author_id = $book['author_id']; $category_id = $book['category_id'];
$publisher = $book['publisher']; $year = $book['year_published'];
$total = $book['total_copies'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isbn        = trim($_POST['isbn'] ?? '');
    $title       = trim($_POST['title'] ?? '');
    $author_id   = $_POST['author_id'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $publisher   = trim($_POST['publisher'] ?? '');
    $year        = trim($_POST['year_published'] ?? '');
    $total       = trim($_POST['total_copies'] ?? '');

    if ($isbn === '')        $errors[] = 'ISBN is required.';
    if ($title === '')       $errors[] = 'Title is required.';
    if ($author_id === '')   $errors[] = 'Please choose an author.';
    if ($category_id === '') $errors[] = 'Please choose a category.';
    if ($total === '' || !ctype_digit($total) || (int)$total < 1)
        $errors[] = 'Total copies must be a whole number of at least 1.';
    if ($year === '' || !ctype_digit($year))
        $errors[] = 'Year published is required and must be a number.';

    // New total cannot be smaller than the copies already on loan
    if (ctype_digit((string)$total) && (int)$total < $copies_out)
        $errors[] = "Total copies cannot be less than $copies_out (copies currently on loan).";

    // ISBN unique, but IGNORE this same book (<> ?)
    if ($isbn !== '') {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM books WHERE isbn = ? AND id <> ?');
        $stmt->execute([$isbn, $id]);
        if ($stmt->fetchColumn() > 0) $errors[] = 'Another book already uses that ISBN.';
    }

    if (count($errors) === 0) {
        // Recompute available = new total minus copies still out
        $new_available = (int) $total - $copies_out;

        $stmt = $pdo->prepare(
            'UPDATE books
             SET isbn = ?, title = ?, author_id = ?, category_id = ?,
                 publisher = ?, year_published = ?,
                 total_copies = ?, available_copies = ?
             WHERE id = ?'
        );
        $stmt->execute([
            $isbn, $title, $author_id, $category_id,
            $publisher,
            (int) $year,
            (int) $total, $new_available,
            $id,
        ]);

        set_message('Book updated.');
        header('Location: index.php');
        exit;
    }
}

$page_title = 'Edit Book';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <h1>Edit Book</h1>
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

        <label for="isbn">ISBN *</label>
        <input type="text" id="isbn" name="isbn" value="<?= e($isbn) ?>" required>

        <label for="title">Title *</label>
        <input type="text" id="title" name="title" value="<?= e($title) ?>" required>

        <label for="author_id">Author *</label>
        <select id="author_id" name="author_id" required>
            <option value="">-- choose --</option>
            <?php foreach ($authors as $a): ?>
                <option value="<?= (int) $a['id'] ?>"
                    <?= ($author_id == $a['id']) ? 'selected' : '' ?>>
                    <?= e($a['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="category_id">Category *</label>
        <select id="category_id" name="category_id" required>
            <option value="">-- choose --</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= (int) $c['id'] ?>"
                    <?= ($category_id == $c['id']) ? 'selected' : '' ?>>
                    <?= e($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="publisher">Publisher</label>
        <input type="text" id="publisher" name="publisher" value="<?= e($publisher) ?>">

        <label for="year_published">Year Published *</label>
        <input type="text" id="year_published" name="year_published" value="<?= e($year) ?>" required>

        <label for="total_copies">Total Copies * <span class="text-muted">(<?= $copies_out ?> on loan now)</span></label>
        <input type="number" id="total_copies" name="total_copies" min="1" value="<?= e($total) ?>" required>

        <br><br>
        <button type="submit" class="btn">Update Book</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
