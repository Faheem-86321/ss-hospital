<?php
// DEBUG: Check if this file is loading
file_put_contents('debug.log', "Root index.php loaded at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
echo "<!-- Root index.php is loading -->";

session_start();
ob_start(); 
// ... rest of your code ...
