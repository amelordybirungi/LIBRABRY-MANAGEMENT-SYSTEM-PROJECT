<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$authors    = $pdo->query('SELECT id, name FROM authors ORDER BY name')->fetchAll();
$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();

$errors = [];

$isbn = ''; $title = ''; $author_id = ''; $category_id = '';
$publisher = ''; $year = ''; $total = '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isbn        = trim($_POST['isbn'] ?? '');
    $title       = trim($_POST['title'] ?? '');
    $author_id   = $_POST['author_id'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $publisher   = trim($_POST['publisher'] ?? '');
    $year        = trim($_POST['year_published'] ?? '');
    $total       = trim($_POST['total_copies'] ?? '');

    if ($isbn === '')                      $errors[] = 'ISBN is required.';
    if ($title === '')                     $errors[] = 'Title is required.';
    if ($author_id === '')                 $errors[] = 'Please choose an author.';
    if ($category_id === '')               $errors[] = 'Please choose a category.';
    if ($total === '' || !ctype_digit($total) || (int)$total < 1)
        $errors[] = 'Total copies must be a whole number of at least 1.';
    if ($year === '' || !ctype_digit($year))
        $errors[] = 'Year published is required and must be a number.';

    // ISBN must be unique
    if ($isbn !== '') {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM books WHERE isbn = ?');
        $stmt->execute([$isbn]);
        if ($stmt->fetchColumn() > 0) $errors[] = 'A book with that ISBN already exists.';
    }

    if (count($errors) === 0) {
        $stmt = $pdo->prepare(
            'INSERT INTO books
                (isbn, title, author_id, category_id, publisher, year_published,
                 total_copies, available_copies)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $isbn, $title, $author_id, $category_id,
            $publisher,
            (int) $year,
            (int) $total,
            (int) $total,          // a brand-new book: all copies available
        ]);

        set_message('Book added.');
        header('Location: index.php');
        exit;
    }
}

$page_title = 'Add Book';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <h1>Add Book</h1>
    <a class="btn btn-secondary" href="index.php">&larr; Back to list</a>
</div>

<?php if (count($errors) > 0): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="form-card">
    <form method="post" action="create.php">
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

        <label for="total_copies">Total Copies *</label>
        <input type="number" id="total_copies" name="total_copies" min="1" value="<?= e($total) ?>" required>

        <br><br>
        <button type="submit" class="btn">Save Book</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
