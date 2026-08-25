<?php 
session_start();
ob_start(); 

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
    <head><title>404 - Page Not Found</title></head>
    <body style="text-align:center; padding:50px; font-family:Arial;">
        <h1>404 - Page Not Found</h1>
        <p>The page "<?php echo htmlspecialchars($_GET['page']); ?>" does not exist.</p>
        <p><a href="?page=login">Go to Login</a></p>
        <div style="background:#f5f5f5; padding:20px; margin-top:20px; text-align:left;">
            <h3>Debug:</h3>
            <p>Looking for: <?php echo htmlspecialchars($_GET['page']); ?></p>
            <p>Files in web_temp/:
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
            } else {
                echo "web_temp folder not found!";
            }
            ?>
            </p>
        </div>
    </body>
    </html>
    <?php
}
?>
