<?php 
session_start();
ob_start(); 

// DEBUG: List all files in web_temp folder
echo "<h2>Debug: Files in web_temp/</h2>";
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
    echo "<p>web_temp folder does not exist!</p>";
}
echo "<hr>";
// END DEBUG

if(!isset($_GET['page'])){
    $_GET['page'] = "login";
}

// ... rest of your code ...
