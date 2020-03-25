<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeoldunick']=="" && $_COOKIE['teatimeunick']==""){
	echo "<script language='javascript'>alert('目前沒有您的訂購紀錄！');location.href='/';</script>";
}

require_once "conn.php";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TeaTime 茶飲訂購系統 檢視訂單</title>
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.tooltip.css" />
<link rel="stylesheet" type="text/css" href="css/easyui.css">
<link rel="stylesheet" type="text/css" href="css/icon.css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="js/common.js" type="text/javascript"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script>
<script src="js/jquery.tooltip.js" type="text/javascript"></script>
<script src="js/jquery.easyui.min.js" type="text/javascript"></script>
<!--<script type="text/javascript" src="js/jquery.colorize-2.0.0.js"></script>-->
<script type="text/javascript">
$(document).ready(function(){
	$('#tabsdiv').tabs();
	$('.tonus').tooltip({delay: 0, showURL: false, bodyHandler: function() {return $("<img/>").attr("src", this.src);}});
	//$(".profiletb").colorize();

});
 
</script>
<style>
.profiletb{ background-color:#F90}
.profiletb td{ border:solid 0px #999999;}
</style>
</head>
<body>

<div id="tabsdiv" style="width:680px;height:auto; padding-left:10px;">
<div title="以明細方式檢視訂單" style="text-align:center;padding:10px;"><div style="text-align:left;padding:10px; font-size:11px;color:#999999;"><ul style="list-style:inside"><li style="list-style:square;">滑鼠移到密碼或品名上，將顯示標題或店名。</li><li style="list-style:square;">使用不同電腦或瀏覽器時，則每台電腦的訂購記錄也會不同。</li><li style="list-style:square;">訂購記錄將為您保留一年。</li></ul></div>
<?
$unickstr=$_COOKIE['teatimeoldunick'];

if($unickstr!="" && strlen($unickstr)>0)
{
	if(strpos($unickstr,"##")) 
	{
		//above 2 nicknames
		$nickarr=explode("##",$unickstr);
		$m=0;
		foreach($nickarr as $nk)
		{
			$nickarr[$m]="'".trim(str_replace("#","",$nk))."'";
			$m++;
		}
		$nickname=implode(",",$nickarr);
	}
	else
	{
		//only one nickname
		$nickname="'".trim(str_replace("#","",$unickstr))."'";
	}
}
else if($unickstr=="" && $_COOKIE['teatimeunick']!="")
{
	$nickname="'".$_COOKIE['teatimeunick']."'";
}


if($nickname!="" && strlen($nickname)>0)
{
	$uflag=$_COOKIE['teatimeuflag'];
	$strSql="SELECT ono,uoption,pqty,unick,ordertime,sname,pname,L.pprice FROM ttlisttb AS L INNER JOIN ttprodtb AS P INNER JOIN ttcatetb AS C INNER JOIN ttstoretb AS S ON C.cno=P.cno AND P.pno=L.pno AND C.sno=S.sno WHERE unick in ($nickname) AND L.uflag='$uflag' ORDER BY ordertime desc,S.sno,C.cno";

	$data_rows=mysql_query($strSql,$link);
	$i=1;
	$tt=mysql_num_rows($data_rows);
	echo "<table border='0' cellpadding='0' cellspacing='1' class='profiletb' width='99%'><tr><th>#</th><th>品名</th><th>密碼</th><th>選項</th><th>訂購人</th><th>數量</th><th>單價</th><th>時間</th></tr>";
	while(list($ono,$uoption,$pqty,$unick,$ordertime,$sname,$pname,$pprice)=mysql_fetch_row($data_rows))
	{
		$strSql="SELECT otitle,oorderkey FROM ttordertb WHERE ono=$ono";
		$data=mysql_query($strSql);
		$rows=mysql_fetch_array($data);
		
		echo "<tr><td>$i</td><td title='$sname'>$pname</td><td title='$rows[0]'>$rows[1]</td><td>$uoption</td><td>$unick</td><td>$pqty</td><td>$pprice</td><td>$ordertime</td></tr>";
		$i++;
	}
	echo "</table>";

?>
	</div>
	<div title="以店家排列檢視訂單" style="text-align:center;padding:10px;">
<?

	$sn="";
	$strSql="SELECT GROUP_CONCAT(CONCAT('&hearts;',unick,':'),REPLACE(uoption,',','-')),sum(pqty),sname,pname,ppic,cname,L.pprice FROM ttlisttb AS L INNER JOIN ttprodtb AS P INNER JOIN ttcatetb AS C INNER JOIN ttstoretb AS S ON C.cno=P.cno AND P.pno=L.pno AND C.sno=S.sno WHERE unick in ($nickname) AND uflag='$ckuflag' GROUP BY sname,cname,pname,ppic ORDER BY ordertime desc,S.sno,C.cno";
	
	$data_rows=mysql_query($strSql,$link);
	$k=0;
	$i=0;
	$tt=mysql_num_rows($data_rows);
	while(list($remark,$pqty,$sname,$pname,$ppic,$cname,$pprice)=mysql_fetch_row($data_rows))
	{
		if($ppic=="") $ppicstr="&nbsp;"; else $ppicstr="<img class='tonus' src='prodpic/$ppic' width='20' height='20' />";
		if($sn!=$sname && $sn!="") echo "</table>";
		if($sn!=$sname || $sn=="") 
		{
			echo "<div style='font-size:16px;font-weight:bold; padding:5px 35px; text-align:left;'><img src='css/images/tree_checkbox_2.gif' />店家:$sname</div><table border='0' cellpadding='0' cellspacing='1' class='profiletb' width='99%'><tr><th>#</th><th>系列</th><th>品名</th><th>選項</th><th>總數</th><th>圖片</th></tr>";
			$sn=$sname;
			if($i>0) $k+=$i; //tmp quantity for the store
			$i=0;
		}
		
		echo "<tr><td>".($i+1)."</td><td>$cname</td><td>$pname</td><td align='left'>".str_replace(",","<br />",$remark)."</td><td>$pqty</td><td>$ppicstr</td></tr>";
		
		if(($k+$i+1)==$tt || $tt==$i) echo "</table>"; //because $i initalized from 1, $tt==$i cos of only one store
		$i++;
		$totalcost +=$pqty*$pprice;
		$totalqty +=$pqty; //rapid sum up
	}

}
?>
</div></div><hr size='1' /><div><span style="color:#F00;">總計</span> : <?=$totalcost;?> 元 / <span style="color:#00F;">總數</span> : <?=$totalqty;?> (單位)</div>
</body>
</html>