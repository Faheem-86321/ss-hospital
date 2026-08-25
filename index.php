<?php 
session_start();
ob_start(); 

if(!isset($_GET['page'])){
    $_GET['page'] = "login";
}

// Try both lowercase and uppercase versions of the filename
$page_lower = "web_temp/". strtolower($_GET['page']) . ".html.php";
$page_upper = "web_temp/". ucfirst(strtolower($_GET['page'])) . ".html.php";

$page_file = null;
if(file_exists($page_lower)) {
    $page_file = $page_lower;
} elseif(file_exists($page_upper)) {
    $page_file = $page_upper;
}

if($page_file) 
{
    /* Common Header */
    $head_title = $_GET['page'];
    
    // Only include if they exist
    if(file_exists("models/logincookie.php")) {
        include_once("models/logincookie.php");
    }
    if(file_exists("env/main_config.php")) {
        include_once("env/main_config.php");
    }
    if(file_exists("common/header.php")) {
        include_once("common/header.php");
    }
    
    include_once($page_file);
    
    if(file_exists("common/footer.php")) {
        include_once("common/footer.php");
    }
} else {
    // 404 Not Found
    ?>
    <!DOCTYPE html>
    <html>
    <head><title>404 - Page Not Found</title></head>
    <body style="text-align:center; padding:50px; font-family:Arial;">
        <h1>404 - Page Not Found</h1>
        <p>The page "<?php echo $_GET['page']; ?>" does not exist.</p>
        <p><a href="?page=login">Go to Login</a></p>
    </body>
    </html>
    <?php
    die();
}
?>
