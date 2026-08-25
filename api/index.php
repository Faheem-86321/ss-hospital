<?php
// This routes all requests to your main index.php
// Preserves the original URL and query parameters

// Get the original request URI
$request_uri = $_SERVER['REQUEST_URI'];

// Include the main index.php
include __DIR__ . '/../index.php';
?>
