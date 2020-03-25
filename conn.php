<?
$db_server="localhost";//資料庫連結網址
$db_name="teatimedb";//資料庫名稱
$db_user="root";//資料庫帳號
$db_pass="i2i2##i2";//資料庫密碼

//mysql
$link=mysql_connect("$db_server","$db_user","$db_pass");
mysql_query("SET NAMES 'utf8'");
mysql_select_db("$db_name",$link);

//mysqli
$link2=mysqli_connect("$db_server","$db_user","$db_pass","$db_name");
mysqli_query($link2,"SET NAMES 'utf8'");

?>