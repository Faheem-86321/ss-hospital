<?php
session_start();

$dbhost = 'localhost';
$dbuser = 'u719432153_Faheem';
$dbpass = '$Bf1Yl=QAYZb';
$dbname = 'u719432153_Faheem';

$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query
$company_sql = "SELECT * FROM company_info WHERE status = '1' AND close = '1' LIMIT 1";
$result = mysqli_query($con, $company_sql);

// Check query
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    $_SESSION['com_name']    = $row['com_name'];
    $_SESSION['com_phone']   = $row['com_phone'];
    $_SESSION['com_email']   = $row['com_email'];
    $_SESSION['com_logo']    = $row['com_logo'];
    $_SESSION['com_address'] = $row['com_address'];

} else {
    echo "No company data found";
}
?>
