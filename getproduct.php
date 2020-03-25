<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}
//if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']=="") exit;

require_once "conn.php";
$cno=$_REQUEST['cno'];

if(!empty($cno))
{
	global $link;
	$strSql="SELECT pno,pname,ppic,pintro FROM ttprodtb WHERE cno=$cno";
	$data_rows=mysql_query($strSql,$link);
	
	$i=0;
	$maxcol=5;
	$tt=mysql_num_rows($data_rows);
	echo "<center><table border='0' cellspacing='1' cellpadding='0'>";
	while(list($cno,$cname,$ppic,$pintro)=mysql_fetch_row($data_rows))
	{
		 if($i%$maxcol==0) echo "<tr>";
		//echo "<td style='text-align:left;' valign='top'><img class='tonus' id='pdpicimg' src='prodpic/$ppic' width='10' height='10' /><label title='$pintro'>$cname</label></td>";
		echo "<td style='text-align:left;' valign='top'><img src='css/images/tree_checkbox_2.gif' width='10' height='10' /><label title='$pintro'>$cname</label></td>";
						
		if($i%$maxcol==($maxcol-1) || $i==($tt-1))  echo "</tr>";
		$i++;
	}
	echo "</table></center>";
}
else
	echo "還沒有相關的產品"; //failure
?>