<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo 0;
	exit;
}

$uno=$_COOKIE['teatimeuserno'];
if($uno=="") {
	echo 0;
	exit;
}

include "conn.php";
$strSql="SELECT unick,I.ono,otitle,oorderkey FROM ttinvitetb I INNER JOIN ttordertb O ON I.uno=O.uno AND I.ono=O.ono WHERE I.uno=$uno AND omailflag<>'y'";
$data_rows=mysql_query($strSql);
echo mysql_num_rows($data_rows);
?>