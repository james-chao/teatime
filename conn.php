<?
$db_server="localhost";//��Ʈw�s�����}
$db_name="teatimedb";//��Ʈw�W��
$db_user="root";//��Ʈw�b��
$db_pass="i2i2##i2";//��Ʈw�K�X

//mysql
$link=mysql_connect("$db_server","$db_user","$db_pass");
mysql_query("SET NAMES 'utf8'");
mysql_select_db("$db_name",$link);

//mysqli
$link2=mysqli_connect("$db_server","$db_user","$db_pass","$db_name");
mysqli_query($link2,"SET NAMES 'utf8'");

?>