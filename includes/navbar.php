<?php

$current = basename(dirname($_SERVER['SCRIPT_NAME']));

// Helper: prints class="active" when $section matches the current folder
function nav_active($section)
{
    global $current;
    return $current === $section ? 'class="active"' : '';
}
?>
<nav class="navbar">
    <div class="navbar-left">
        <a class="navbar-brand" href="../dashboard/index.php">
            <img src="../assets/images/logo-01.png" alt="Library System" class="navbar-logo">
        </a>
    </div>

    <button class="navbar-toggle" type="button" aria-label="Toggle menu"
            aria-expanded="false" aria-controls="navbarMenu">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
    </button>

    <ul class="navbar-menu" id="navbarMenu">
        <li><a <?= nav_active('dashboard') ?>  href="../dashboard/index.php">Dashboard</a></li>
        <li><a <?= nav_active('books') ?>      href="../books/index.php">Books</a></li>
        <li><a <?= nav_active('members') ?>    href="../members/index.php">Members</a></li>
        <li><a <?= nav_active('loans') ?>      href="../loans/index.php">Loans</a></li>
        <li><a <?= nav_active('authors') ?>    href="../authors/index.php">Authors</a></li>
        <li><a <?= nav_active('categories') ?> href="../categories/index.php">Categories</a></li>
        <?php // "Users" is only for admins ?>
        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
            <li><a <?= nav_active('users') ?> href="../users/index.php">Users</a></li>
        <?php endif; ?>
    </ul>

    <div class="navbar-right">
        <!-- Search: a magnifier icon that expands into an input when clicked.
             Clicking the label focuses the input, which expands it (CSS :focus-within).
             Submits to the search page as ?q=... -->
        <form class="navbar-search" method="get" action="../search/index.php" role="search">
            <label class="navbar-search-icon" for="navSearchInput" aria-label="Search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </label>
            <input id="navSearchInput" type="text" name="q" placeholder="Search..."
                   value="<?= e($_GET['q'] ?? '') ?>" aria-label="Search the system">
        </form>

        <div class="navbar-user">
            <!-- e() makes the name safe to print as HTML -->
            <span>Hi, <?= e($_SESSION['full_name'] ?? 'User') ?></span>
            <a class="btn btn-small" href="../auth/logout.php">Logout</a>
        </div>
    </div>
</nav>
