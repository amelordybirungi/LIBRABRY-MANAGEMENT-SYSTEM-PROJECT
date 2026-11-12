<?php

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

// Only members who can borrow, and only books with a copy free
$members = $pdo->query(
    "SELECT id, member_number, full_name
     FROM members WHERE status = 'active' ORDER BY full_name"
)->fetchAll();

$books = $pdo->query(
    'SELECT id, title, available_copies
     FROM books WHERE available_copies > 0 ORDER BY title'
)->fetchAll();

$errors = [];
$member_id = ''; $book_id = '';
$date_issued = date('Y-m-d');
$due_date    = date('Y-m-d', strtotime('+14 days'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id   = $_POST['member_id'] ?? '';
    $book_id     = $_POST['book_id'] ?? '';
    $date_issued = $_POST['date_issued'] ?? '';
    $due_date    = $_POST['due_date'] ?? '';

    if ($member_id === '')   $errors[] = 'Please choose a member.';
    if ($book_id === '')     $errors[] = 'Please choose a book.';
    if ($date_issued === '') $errors[] = 'Issue date is required.';
    if ($due_date === '')    $errors[] = 'Due date is required.';
    if ($date_issued !== '' && $due_date !== '' && $due_date < $date_issued)
        $errors[] = 'Due date cannot be before the issue date.';

    // ---- Re-check the member is really active ----
    if ($member_id !== '') {
        $stmt = $pdo->prepare("SELECT status FROM members WHERE id = ?");
        $stmt->execute([$member_id]);
        $m = $stmt->fetch();
        if (!$m)                         $errors[] = 'That member does not exist.';
        elseif ($m['status'] !== 'active') $errors[] = 'That member is suspended and cannot borrow.';
    }

    // ---- Re-check the book still has a copy free ----
    if ($book_id !== '') {
        $stmt = $pdo->prepare('SELECT available_copies FROM books WHERE id = ?');
        $stmt->execute([$book_id]);
        $b = $stmt->fetch();
        if (!$b)                              $errors[] = 'That book does not exist.';
        elseif ((int) $b['available_copies'] < 1) $errors[] = 'No copies of that book are available.';
    }

    // A member cannot have the same book on loan twice at the same time:
    // block a second copy while an earlier one is still unreturned.
    if ($member_id !== '' && $book_id !== '') {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM loans
             WHERE member_id = ? AND book_id = ? AND date_returned IS NULL'
        );
        $stmt->execute([$member_id, $book_id]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'This member already has this book on loan. It must be returned first.';
        }
    }

    // ---- Save: both steps together, or neither ----
    if (count($errors) === 0) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'INSERT INTO loans (member_id, book_id, date_issued, due_date)
                 VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([$member_id, $book_id, $date_issued, $due_date]);

            $stmt = $pdo->prepare(
                'UPDATE books SET available_copies = available_copies - 1 WHERE id = ?'
            );
            $stmt->execute([$book_id]);

            $pdo->commit();   // make both changes permanent

            set_message('Book issued.');
            header('Location: index.php');
            exit;
        } catch (Exception $ex) {
            $pdo->rollBack();   // undo everything if anything failed
            $errors[] = 'Could not issue the book. Please try again.';
        }
    }
}

$page_title = 'Issue a Book';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <h1>Issue a Book</h1>
    <a class="btn btn-secondary" href="index.php">&larr; Back to list</a>
</div>

<?php if (count($errors) > 0): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="form-card">
    <form method="post" action="issue.php">
        <label for="member_id">Member *</label>
        <select id="member_id" name="member_id" required>
            <option value="">-- choose --</option>
            <?php foreach ($members as $m): ?>
                <option value="<?= (int) $m['id'] ?>"
                    <?= ($member_id == $m['id']) ? 'selected' : '' ?>>
                    <?= e($m['member_number']) ?> — <?= e($m['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="book_id">Book *</label>
        <select id="book_id" name="book_id" required>
            <option value="">-- choose --</option>
            <?php foreach ($books as $b): ?>
                <option value="<?= (int) $b['id'] ?>"
                    <?= ($book_id == $b['id']) ? 'selected' : '' ?>>
                    <?= e($b['title']) ?> (<?= (int) $b['available_copies'] ?> free)
                </option>
            <?php endforeach; ?>
        </select>

        <label for="date_issued">Issue Date *</label>
        <input type="date" id="date_issued" name="date_issued" value="<?= e($date_issued) ?>" required>

        <label for="due_date">Due Date *</label>
        <input type="date" id="due_date" name="due_date" value="<?= e($due_date) ?>" required>

        <br><br>
        <button type="submit" class="btn">Issue Book</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
