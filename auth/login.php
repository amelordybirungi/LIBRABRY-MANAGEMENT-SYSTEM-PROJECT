// 1. Read what the user typed
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// 2. Validate
if ($username === '' || $password === '') {
    $error = 'Please enter both username and password.';
} else {
    // 3. Find the user with a PREPARED STATEMENT (the ? keeps input out of the SQL)
    $stmt = $pdo->prepare('SELECT user_id, password_hash, full_name, role
                           FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);   // the row, or false if not found

    // 4. Compare the typed password with the stored hash
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);           // fresh session ID (security)

        // 5. Remember the user
        $_SESSION['user_id']   = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role']      = $user['role'];

        set_message('Welcome, ' . $user['full_name'] . '!');
        header('Location: ../dashboard/index.php');
        exit;
    } else {
        $error = 'Wrong username or password.';  // same message for both = safer
    }
}