<?php 
session_start();
ob_start(); 

// If no page specified, use login
if(!isset($_GET['page'])){
    $_GET['page'] = "login";
}

// Try both lowercase and uppercase
$page_lower = 'web_temp/' . strtolower($_GET['page']) . '.html.php';
$page_upper = 'web_temp/' . ucfirst(strtolower($_GET['page'])) . '.html.php';

$page_file = null;
if(file_exists($page_lower)) {
    $page_file = $page_lower;
} elseif(file_exists($page_upper)) {
    $page_file = $page_upper;
}

if($page_file) {
    include_once($page_file);
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>404 - Page Not Found</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 30px; background: #f5f5f5; }
            .debug { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: 20px 0; }
            .debug ul { list-style: none; padding: 0; }
            .debug li { padding: 5px 10px; background: #f9f9f9; margin: 3px 0; border-radius: 4px; }
        </style>
    </head>
    <body>
        <h1>404 - Page Not Found</h1>
        <p>The page "<?php echo htmlspecialchars($_GET['page']); ?>" does not exist.</p>
        <p><a href="?page=login">Go to Login</a></p>
        
        <div class="debug">
            <h3>Debug Information</h3>
            <p><strong>Looking for:</strong> <?php echo htmlspecialchars($_GET['page']); ?></p>
            <p><strong>Files in web_temp/:</strong></p>
            <?php
            if(is_dir("web_temp")) {
                $files = scandir("web_temp");
                echo "<ul>";
                foreach($files as $file) {
                    if($file != "." && $file != "..") {
                        echo "<li>" . $file . "</li>";
                    }
                }
                echo "</ul>";
                echo "<p>Total files: " . (count($files) - 2) . "</p>";
            } else {
                echo "<p style='color:red;'>web_temp folder does not exist!</p>";
                echo "<p>Current directory: " . __DIR__ . "</p>";
                echo "<p>Files in current directory:</p>";
                echo "<ul>";
                foreach(scandir(__DIR__) as $file) {
                    if($file != "." && $file != "..") {
                        echo "<li>" . $file . "</li>";
                    }
                }
                echo "</ul>";
            }
            ?>
        </div>
    </body>
    </html>
    <?php
}
?>
