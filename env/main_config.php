<?php 
$dbhost = 'localhost';
$dbuser = 'u719432153_Faheem';
$dbpass = 'w4V90LL*f&:';
$dbname = 'u719432153_Faheem';
$tables = '*';
$con  = mysqli_connect($dbhost,$dbuser,$dbpass, $dbname);
if($con == false){
	echo "Connection Not Established";
}
else{
	$company_sql = "SELECT * FROM company_info WHERE status = '1' AND close = '1' ";
	$company_sql_ex = mysqli_query($con,$company_sql);
	foreach ($company_sql_ex as $key) {
		$_SESSION['com_name'] = $key['com_name'];
		$_SESSION['com_phone'] = $key['com_phone'];
		$_SESSION['com_email'] = $key['com_email'];
		$_SESSION['com_logo'] = $key['com_logo'];
		$_SESSION['com_address'] = $key['com_address'];
	}

}
?>
