<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}
include "conn.php";

$uno=$_COOKIE['teatimeuserno'];
$unick=$_COOKIE['teatimeunick'];

$strSql="SELECT unick,I.ono,otitle,oorderkey FROM ttinvitetb I INNER JOIN ttordertb O ON I.uno=O.uno AND I.ono=O.ono WHERE I.ufname='$unick' AND omailflag<>'y'";
$data_rows=mysql_query($strSql);
echo "<div style='text-align:right;padding:2px;cursor:pointer'><img alt='關閉' title='關閉' src='css/images/tabs_close.gif' id='hiboxclose' /></div>";
echo "<ul>";
while(list($unick,$ono,$otitle,$oorderkey)=mysql_fetch_row($data_rows))
{
	echo "<li>[$unick]: <a href='ordermenu.php?ono=$ono&ok=$oorderkey'>$otitle</a></li>";
}
echo "</ul>";
?>
<script>
$(document).ready(function(){
	$('#hiboxclose').click(function(){
		$('#hibox').hide();
	});
});
</script>