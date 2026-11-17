<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// e() = make text safe before printing it in HTML
function e($text)
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

// Save a message to show on the NEXT page ('success' = green, 'danger' = red)
function set_message($text, $type = 'success')
{
    $_SESSION['message'] = ['text' => $text, 'type' => $type];
}

<<<<<<< HEAD
=======
// The "super admin" is the built-in account with username 'admin'.
// Only the super admin may add, edit or delete system users.
function is_super_admin()
{
    return ($_SESSION['username'] ?? '') === 'admin';
}

>>>>>>> 7c794f68c02dd4ad8dd351b3b3e2318287546479
// Print the saved message once, then forget it.
// It shows as a "toast" popup (top-right) that slides in and fades away by itself.
// The small JS in footer.php auto-dismisses it after a few seconds.
function show_message()
{
    if (isset($_SESSION['message'])) {
        $msg = $_SESSION['message'];
        echo '<div class="toast toast-' . e($msg['type']) . '" role="alert">'
           . '<span class="toast-text">' . e($msg['text']) . '</span>'
           . '<button type="button" class="toast-close" aria-label="Dismiss"'
           . ' onclick="this.parentNode.remove()">'
           . '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"'
           . ' stroke-width="2" stroke-linecap="round" stroke-linejoin="round">'
           . '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>'
           . '</button></div>';
        unset($_SESSION['message']);
    }
}

// Where member photos live, as an absolute server path.
define('MEMBER_PHOTO_DIR', __DIR__ . '/../assets/uploads/members');

// The image types we accept, mapped to the file extension we save them as.
// We read the REAL type from the file itself (getimagesize), never from the
// browser-supplied name or MIME, so a renamed ".php" cannot sneak in.
function member_photo_allowed_types()
{
    return [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_GIF  => 'gif',
        IMAGETYPE_WEBP => 'webp',
    ];
}

/**
 * Validate and store an uploaded member photo.
 *
 * @param array  $file   One entry from $_FILES (e.g. $_FILES['photo']).
 * @param string $error  Filled with a human message if something is wrong.
 * @return string|null   The saved file name to store in the DB, or null.
 *                       null + empty $error means "no file was uploaded" (fine).
 *                       null + a message means the upload was rejected.
 */
function save_member_photo($file, &$error)
{
    $error = '';

    // No file chosen is perfectly valid — the photo is optional.
    if (!isset($file) || !is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    // Any other PHP upload error (too big, partial, no temp dir, ...).
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'The photo failed to upload. Please try again.';
        return null;
    }

    // Guard against spoofed paths: the file must really be an HTTP upload.
    if (!is_uploaded_file($file['tmp_name'])) {
        $error = 'Invalid photo upload.';
        return null;
    }

    // Size limit: 2 MB.
    $max_bytes = 2 * 1024 * 1024;
    if ($file['size'] > $max_bytes) {
        $error = 'The photo is too large (maximum 2 MB).';
        return null;
    }

    // Read the actual image type from the file contents.
    $info = @getimagesize($file['tmp_name']);
    $allowed = member_photo_allowed_types();
    if ($info === false || !isset($allowed[$info[2]])) {
        $error = 'The photo must be a JPG, PNG, GIF or WEBP image.';
        return null;
    }
    $ext = $allowed[$info[2]];

    // Build a safe, unguessable file name. We never reuse the original name.
    $name = 'm' . time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;

    if (!is_dir(MEMBER_PHOTO_DIR)) {
        @mkdir(MEMBER_PHOTO_DIR, 0755, true);
    }

    $target = MEMBER_PHOTO_DIR . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        $error = 'Could not save the photo on the server.';
        return null;
    }

    return $name;
}

// Delete a stored member photo file (if the name is set and the file exists).
function delete_member_photo($filename)
{
    if (!$filename) {
        return;
    }
    // Only ever touch a bare file name inside our own folder.
    $filename = basename($filename);
    $path = MEMBER_PHOTO_DIR . '/' . $filename;
    if (is_file($path)) {
        @unlink($path);
    }
}

// Two-letter initials from a name, e.g. "Alice Smith" -> "AS".
function member_initials($name)
{
    $parts = preg_split('/\s+/', trim((string) $name));
    if (!$parts || $parts[0] === '') {
        return '?';
    }
    $first = mb_substr($parts[0], 0, 1);
    $last  = count($parts) > 1 ? mb_substr($parts[count($parts) - 1], 0, 1) : '';
    return mb_strtoupper($first . $last);
}

/**
 * Print a member avatar: the uploaded photo, or an initials circle if none.
 *
 * @param string|null $photo    File name stored in the DB (or null/empty).
 * @param string      $name     Member's full name (for initials + alt text).
 * @param int         $size     Pixel size of the square/circle.
 * @param string      $base     Path prefix to the project root ('../' in subfolders).
 */
function member_avatar($photo, $name, $size = 40, $base = '../')
{
    $px = (int) $size;
    if ($photo) {
        $src = $base . 'assets/uploads/members/' . rawurlencode(basename($photo));
        echo '<img class="avatar" src="' . e($src) . '" alt="' . e($name) . '"'
           . ' style="width:' . $px . 'px;height:' . $px . 'px;">';
    } else {
        echo '<span class="avatar avatar-initials" aria-hidden="true"'
           . ' style="width:' . $px . 'px;height:' . $px . 'px;font-size:' . round($px * 0.4) . 'px;">'
           . e(member_initials($name))
           . '</span>';
    }
}