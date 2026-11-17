<?php

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$total_books   = $pdo->query('SELECT COUNT(*) FROM books')->fetchColumn();
$total_members = $pdo->query('SELECT COUNT(*) FROM members')->fetchColumn();
$books_out     = $pdo->query('SELECT COUNT(*) FROM loans WHERE date_returned IS NULL')->fetchColumn();
$overdue_count = $pdo->query(
    'SELECT COUNT(*) FROM loans WHERE date_returned IS NULL AND due_date < CURDATE()'
)->fetchColumn();

$overdue_loans = $pdo->query(
    'SELECT l.id, l.due_date,
            m.full_name AS member_name,
            b.title     AS book_title,
            DATEDIFF(CURDATE(), l.due_date) AS days_over
     FROM loans l
     JOIN members m ON m.id = l.member_id
     JOIN books   b ON b.id = l.book_id
     WHERE l.date_returned IS NULL AND l.due_date < CURDATE()
     ORDER BY l.due_date ASC'
)->fetchAll();

$recent_loans = $pdo->query(
    'SELECT l.date_issued, l.due_date, l.date_returned,
            m.full_name AS member_name,
            b.title     AS book_title
     FROM loans l
     JOIN members m ON m.id = l.member_id
     JOIN books   b ON b.id = l.book_id
     ORDER BY l.date_issued DESC, l.id DESC
     LIMIT 5'
)->fetchAll();

$today = date('Y-m-d');

$page_title = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="page-greeting">Welcome back, <?= e($_SESSION['full_name'] ?? 'User') ?></h1>
<p class="page-date">
    <time id="currentDateTime" datetime="<?= e(date(DATE_ATOM)) ?>" data-live-clock>
        <?= e(date('l, j F Y g:i:s A')) ?>
    </time>
</p>

<div class="stats-h stats-h-img">
    <div class="stat-h teal">
        <div class="stat-h-icon">
            <img src="../assets/images/dashboard-icons/books.png" alt="">
        </div>
        <div>
            <div class="stat-h-num" data-target="<?= (int) $total_books ?>">0</div>
            <div class="stat-h-label">Total Books</div>
        </div>
    </div>

    <div class="stat-h blue">
        <div class="stat-h-icon">
            <img src="../assets/images/dashboard-icons/members.png" alt="">
        </div>
        <div>
            <div class="stat-h-num" data-target="<?= (int) $total_members ?>">0</div>
            <div class="stat-h-label">Members</div>
        </div>
    </div>

    <div class="stat-h amber">
        <div class="stat-h-icon">
            <img src="../assets/images/dashboard-icons/out.png" alt="">
        </div>
        <div>
            <div class="stat-h-num" data-target="<?= (int) $books_out ?>">0</div>
            <div class="stat-h-label">Books Out</div>
        </div>
    </div>

    <div class="stat-h red">
        <div class="stat-h-icon">
            <img src="../assets/images/dashboard-icons/overdue.png" alt="">
        </div>
        <div>
            <div class="stat-h-num" data-target="<?= (int) $overdue_count ?>">0</div>
            <div class="stat-h-label">Overdue</div>
        </div>
    </div>
</div>

<h2>Overdue Loans</h2>
<?php if (count($overdue_loans) === 0): ?>
    <p class="text-muted">Nothing overdue. All books are on time.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Member</th>
                <th>Book</th>
                <th>Due Date</th>
                <th>Days Late</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($overdue_loans as $row): ?>
                <tr>
                    <td><?= e($row['member_name']) ?></td>
                    <td><?= e($row['book_title']) ?></td>
                    <td><?= e($row['due_date']) ?></td>
                    <td><span class="badge badge-overdue"><?= (int) $row['days_over'] ?> days</span></td>
                    <td>
                        <form method="post" action="../loans/return.php" style="display:inline"
                              onsubmit="return confirm('Mark this book as returned?');">
                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                            <button type="submit" class="btn btn-small">Return</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<h2 style="margin-top:28px;">Recent Loans</h2>
<?php if (count($recent_loans) === 0): ?>
    <p class="text-muted">No loans yet.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Member</th>
                <th>Book</th>
                <th>Issued</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_loans as $row): ?>
                <?php
                    if ($row['date_returned'] !== null) { $status = 'returned'; $badge = 'active'; }
                    elseif ($row['due_date'] < $today) { $status = 'overdue';  $badge = 'overdue'; }
                    else                               { $status = 'out';      $badge = 'suspended'; }
                ?>
                <tr>
                    <td><?= e($row['member_name']) ?></td>
                    <td><?= e($row['book_title']) ?></td>
                    <td><?= e($row['date_issued']) ?></td>
                    <td><span class="badge badge-<?= $badge ?>"><?= $status ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<h2 style="margin-top:28px;">Quick Links</h2>
<p>
    <a class="btn" href="../books/index.php">Manage Books</a>
    <a class="btn" href="../members/index.php">Manage Members</a>
    <a class="btn" href="../loans/issue.php">Issue a Book</a>
</p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
