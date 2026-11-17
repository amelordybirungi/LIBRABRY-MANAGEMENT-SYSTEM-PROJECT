<?php

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$today = date('Y-m-d');

$status = $_GET['status'] ?? '';

// Loan status isn't stored in a column, so we express each one as a condition.
$where  = [];
$params = [];

if ($status === 'returned') {
    $where[] = 'l.date_returned IS NOT NULL';
} elseif ($status === 'overdue') {
    $where[]  = 'l.date_returned IS NULL AND l.due_date < ?';
    $params[] = $today;
} elseif ($status === 'out') {
    $where[]  = 'l.date_returned IS NULL AND l.due_date >= ?';
    $params[] = $today;
}

$sql = 'SELECT l.id, l.date_issued, l.due_date, l.date_returned,
               m.full_name AS member_name,
               b.title     AS book_title
        FROM loans l
        JOIN members m ON m.id = l.member_id
        JOIN books   b ON b.id = l.book_id';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY l.date_issued DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$loans = $stmt->fetchAll();

$filtering = ($status !== '');

$page_title = 'Loans';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="toolbar">
    <h1>Loans</h1>
    <a class="btn" href="issue.php">+ Issue a Book</a>
</div>

<!-- Filter bar: submits back to this page with the chosen filters in the URL -->
<form class="filter-bar" method="get" action="index.php">
    <label>
        Status
        <select name="status" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="out"      <?= $status === 'out'      ? 'selected' : '' ?>>Out (not due)</option>
            <option value="overdue"  <?= $status === 'overdue'  ? 'selected' : '' ?>>Overdue</option>
            <option value="returned" <?= $status === 'returned' ? 'selected' : '' ?>>Returned</option>
        </select>
    </label>
    <?php if ($filtering): ?>
        <a class="btn btn-small btn-secondary" href="index.php">Clear</a>
    <?php endif; ?>
</form>

<?php if (count($loans) === 0): ?>
    <p class="text-muted">
        <?= $filtering ? 'No loans match your filter.' : 'No loans yet. Click “Issue a Book” to lend one out.' ?>
    </p>
<?php else: ?>
    <p class="text-muted" style="margin-bottom:12px;">
        Showing <?= count($loans) ?> loan<?= count($loans) === 1 ? '' : 's' ?><?= $filtering ? ' (filtered)' : '' ?>.
    </p>
    <table>
        <thead>
            <tr>
                <th>Member</th>
                <th>Book</th>
                <th>Issued</th>
                <th>Due</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($loans as $row): ?>
                <?php
                    if ($row['date_returned'] !== null) {
                        $status_label = 'returned'; $badge = 'active';
                    } elseif ($row['due_date'] < $today) {
                        $status_label = 'overdue';  $badge = 'overdue';
                    } else {
                        $status_label = 'out';      $badge = 'suspended';
                    }
                ?>
                <tr>
                    <td><?= e($row['member_name']) ?></td>
                    <td><?= e($row['book_title']) ?></td>
                    <td><?= e($row['date_issued']) ?></td>
                    <td><?= e($row['due_date']) ?></td>
                    <td><span class="badge badge-<?= $badge ?>"><?= $status_label ?></span></td>
                    <td>
                        <?php if ($row['date_returned'] === null): ?>
                            <form method="post" action="return.php" style="display:inline"
                                  onsubmit="return confirm('Mark this book as returned?');">
                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                <button type="submit" class="btn btn-small">Return</button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted">Returned <?= e($row['date_returned']) ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
