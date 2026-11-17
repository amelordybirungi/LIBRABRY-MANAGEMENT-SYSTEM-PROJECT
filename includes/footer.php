<?php
?>
</main>

<footer class="footer">
    <div class="footer-top">
        <div class="footer-brand-col">
            <a class="footer-logo" href="../dashboard/index.php">
                <img src="../assets/images/logo-01.png" alt="Library System" class="footer-logo-img">
            </a>
            <p class="footer-blurb">
                MOVE library system designed for managing a library's books, members and loans.
                This is one of the most rare library systems that is user-friendly.
            </p>
        </div>

        <nav class="footer-cols">
            <div class="footer-col">
                <h4>Catalog</h4>
                <a href="../books/index.php">Books</a>
                <a href="../authors/index.php">Authors</a>
                <a href="../categories/index.php">Categories</a>
            </div>
            <div class="footer-col">
                <h4>Circulation</h4>
                <a href="../loans/index.php">Loans</a>
                <a href="../loans/issue.php">Issue a Book</a>
                <a href="../members/index.php">Members</a>
            </div>
            <div class="footer-col">
                <h4>System</h4>
                <a href="../dashboard/index.php">Dashboard</a>
                <a href="../search/index.php">Search</a>
                <a href="../users/index.php">Users</a>
            </div>
            <div class="footer-col">
                <h4>About</h4>
                <span class="footer-info">Version 1.0</span>
                <span class="footer-info">PHP &amp; MySQL</span>
                <span class="footer-info">For library staff</span>
            </div>
        </nav>
    </div>

    <div class="footer-bottom">
        <span>&copy; <?= date('Y') ?> Library Management System. All rights reserved.</span>
        
    </div>
</footer>

<script src="../assets/js/app.js?v=<?= @filemtime(__DIR__ . '/../assets/js/app.js') ?>"></script>

</body>
</html>
