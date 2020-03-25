<?php

$db_server="10.243.22.39";//資料庫連結網址
$db_name="reactor";//資料庫名稱
$db_user="iot";//資料庫帳號
$db_pass="iot1234";//資料庫密碼

//mysql
$link=mysql_connect("$db_server","$db_user","$db_pass");
mysql_query("SET NAMES 'utf8'");
mysql_select_db("$db_name",$link);



$strSql="SELECT * from item";

$data_rows=mysql_query($strSql,$link);
if($rows = mysql_fetch_array($data_rows))
{
	print_r($rows);

}
echo mysql_error();