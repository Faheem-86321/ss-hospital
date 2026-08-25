<?php
// Get the requested path
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Remove leading slash for easier matching
$path = ltrim($path, '/');

// Define routes to map to your PHP files
$routes = [
    '' => 'index.php',                          // Home page
    'admin' => 'admin/index.php',               // Admin panel
    'receptionist' => 'receptionist/index.php', // Receptionist
    'day_incharge' => 'day_incharge/index.php', // Day Incharge
];

// Check if the path matches any route
$found = false;
foreach ($routes as $route => $file) {
    if ($path === $route || strpos($path, $route . '/') === 0) {
        $found = true;
        $file_path = __DIR__ . '/../' . $file;
        if (file_exists($file_path)) {
            include $file_path;
        } else {
            http_response_code(404);
            echo "File not found: " . $file;
        }
        break;
    }
}

// If no route matched, try to serve the file directly
if (!$found) {
    // Check if it's a PHP file
    if (strpos($path, '.php') !== false) {
        $file_path = __DIR__ . '/../' . $path;
        if (file_exists($file_path)) {
            include $file_path;
        } else {
            http_response_code(404);
            echo "Page not found";
        }
    } 
    // Check if it's a static file (CSS, JS, images)
    else {
        $static_path = __DIR__ . '/../' . $path;
        if (file_exists($static_path) && !is_dir($static_path)) {
            // Serve static files
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
            $ext = pathinfo($static_path, PATHINFO_EXTENSION);
            if (isset($mime_types[$ext])) {
                header('Content-Type: ' . $mime_types[$ext]);
            }
            readfile($static_path);
        } else {
            // Default to index.php
            include __DIR__ . '/../index.php';
        }
    }
}
?>
