<?php
/**
 * Resolves a file path case-insensitively.
 * Needed because this app was built/tested on Windows (case-insensitive
 * filesystem) but Vercel deploys on Linux (case-sensitive). $_GET['page']
 * values like "dashboard" or "login" won't match actual files like
 * "Dashboard.php" or "Login.html.php" without this.
 *
 * Returns the real, correctly-cased path if found, or false if no match.
 */
function ci_path($path) {
    if (file_exists($path)) {
        return $path;
    }

    $dir = dirname($path);
    $file = basename($path);

    if (!is_dir($dir)) {
        return false;
    }

    foreach (scandir($dir) as $entry) {
        if (strcasecmp($entry, $file) === 0) {
            return $dir . '/' . $entry;
        }
    }

    return false;
}