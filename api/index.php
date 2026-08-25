<?php
// Get the full request URI with query parameters
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$query = parse_url($request_uri, PHP_URL_QUERY);

// Remove leading slash for easier matching
$path = ltrim($path, '/');

// If it's a static file (CSS, JS, images), serve it directly
$static_extensions = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'ico', 'pdf'];
$ext = pathinfo($path, PATHINFO_EXTENSION);
if (in_array($ext, $static_extensions)) {
    $file_path = __DIR__ . '/../' . $path;
    if (file_exists($file_path) && !is_dir($file_path)) {
        $mime_types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
        ];
        if (isset($mime_types[$ext])) {
            header('Content-Type: ' . $mime_types[$ext]);
        }
        readfile($file_path);
        exit;
    }
}

// For all other requests, include the main index.php
// This preserves the original URL and query string
include __DIR__ . '/../index.php';
?>
