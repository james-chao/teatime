<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TeaTime 茶飲訂購系統 商品排行</title>
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link href="css/cloudcarousel.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.tooltip.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script>
<script src="js/common.js" type="text/javascript"></script>
<script src="js/jquery.tooltip.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('.prodname').tooltip().click(function(){
		var pno=$(this).attr('subid');
		var pratesel=$(this).children('img').attr('subid');
		$.post("rateprod.php",{pno:pno,pratesel:pratesel},function(ret){
			if(ret==true) alert('投票成功'); else alert('您已投過此商品,或您尚未登入');
		});
	});
	
	$('.vote').click(function(){
		var pno=$(this).attr('subid');
		var pratesel=$(this).children('img').attr('subid');
		$.post("rateprod.php",{pno:pno,pratesel:pratesel},function(ret){
			if(ret==true) alert('投票成功'); else alert('您尚未登入,或您已投過此商品');
		});
	}).mouseover(function(){
		$(this).children('img').fadeIn();
	}).mouseout(function(){
		$(this).children('img').fadeOut();
	}).children('img').hide().attr('alt','投此一票');
	
	

});
</script>

<style>
.profiletb td{ border:solid 1px #999999; text-align:center;}
.public{text-align:center;}
.prodname{color:#0000ff; font-weight:bold; cursor:pointer;}
</style>
</head>
<BODY>
<div id="topdiv"><div style="width:385px;"><img src="images/public1.jpg" width="385" height="220" alt=""></div><div style="width:385px;  position:relative; left:385px; top:-220px;"><img src="images/public2.jpg" width="385" height="220" alt=""></div></div>
<div id="leftdiv"><div style="width:50px; height:149px; "><img src="images/sub01_01.jpg" width="250" height="149" id="getT" /></div>
<div id="newsdiv"><span style="text-align:left;"><img src="images/title_public.gif" /></span></div>
<div id="rightdiv">
<?
require_once "conn.php";

	$strSql = "SELECT P.pno,pname,ppic,pintro,sname,SUM(pqty),COUNT(*) AS CT,pgood,pnormal,pbad FROM ttlisttb AS L INNER JOIN ttstoretb AS S INNER JOIN ttprodtb AS P ON L.pno=P.pno AND S.sno=P.sno GROUP BY pname ORDER BY CT desc LIMIT 50";
	$data_rows=mysql_query($strSql,$link);
	while(list($pno,$pname,$ppic,$pintro,$sname,$pqty,$count,$pgood,$pnormal,$pbad)=mysql_fetch_row($data_rows))
	{
		$poarr[]=$pno;
		$pnarr[]=$pname;
		
		if($ppic!="")
			$pdata[]="$sname-$pname,被訂購了 $count 次<br /><img src='prodpic/$ppic'><br />$pintro<br />讚:$pgood 票/ 普:$pnormal 票 /爛:$pbad 票";
		else
			$pdata[]="$sname-$pname,被訂購了 $count 次<br />$pintro<br />讚:$pgood 票/ 普:$pnormal 票 /爛:$pbad 票";	
	}
?>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="47" valign="top" background="images/sub01_19.gif"><img src="images/sub01_07-08.gif" width="47" height="28" alt="" /></td>
      <td valign="top" style="background-image:url(images/sub01_09.gif); background-position:top;">&nbsp;</td>
      <td width="24" height="28" valign="top" background="images/sub01_14.gif"><img src="images/sub01_10.gif" width="24" height="28" alt="" /></td>
    </tr>
    <tr>
      <td valign="top" background="images/sub01_19.gif"><img src="images/sub01_09-13.gif" width="47" height="121" alt="" /></td>
      <td valign="top"><table width="96%" border='0' align='center' cellpadding='0' cellspacing='0' class='public'>
					<tr>
					  <td width='6' height='6'><img src='images/new_box02_top_left.gif'  width='6' height='6' /></td>
					  <td background='images/new_box02_top_bg.gif'><img src='images/blank.gif'  width='1' height='1' /></td>
					  <td width='6' height='6'><img src='images/new_box02_top_right.gif'  width='6' height='6' /></td>
					</tr>
					<tr><td background='images/new_box02_left_bg.gif'></td>
					<td ><div><img src="images/pubbanner.gif" /></div><table cellspacing='0' cellpadding='0' width='100%' border='0'>
					  <tr>
						<td valign="bottom" class="prodname" title="<?=$pdata[1];?>" subid="<?=$poarr[1];?>"><img src="images/second.png" /></td>
						<td class="prodname" title="<?=$pdata[0];?>" subid="<?=$poarr[0];?>"><img src="images/first.jpg" /></td>
						<td valign="bottom" class="prodname" title="<?=$pdata[2];?>" subid="<?=$poarr[2];?>"><img src="images/third.png" /></td>
					  </tr>
					  <tr>
					    <td><b>第二名</b></td>
					    <td><b style='color:#ff0000;'>第一名</b></td>
					    <td><b>第三名</b></td>
				      </tr>
					  <tr>
					    <td class="prodname" title="<?=$pdata[1];?>" subid='<?=$poarr[1];?>'><?=$pnarr[1];?><br /><img src='images/btngood.gif' subid='pgood'> <img src='images/btnnormal.gif' subid='pnormal'> <img src='images/btnbad.gif' subid='pbad'></td>
					    <td class="prodname" title="<?=$pdata[0];?>" subid='<?=$poarr[0];?>'><?=$pnarr[0];?><br /><img src='images/btngood.gif' subid='pgood'> <img src='images/btnnormal.gif' subid='pnormal'> <img src='images/btnbad.gif' subid='pbad'></td>
					    <td class="prodname" title="<?=$pdata[2];?>" subid='<?=$poarr[2];?>'><?=$pnarr[2];?><br /><img src='images/btngood.gif' subid='pgood'> <img src='images/btnnormal.gif' subid='pnormal'> <img src='images/btnbad.gif' subid='pbad'></td>
				      </tr>
					  </table></td>
				  <td background='images/new_box02_right_bg.gif'></td>
				</tr>
				<tr>
				  <td><img src='images/new_box02_bottom_left.gif'  width='6' height='6' /></td>
				  <td background='images/new_box02_bottom_bg.gif'><img src='images/blank.gif'  width='1' height='1' /></td>
				  <td><img src='images/new_box02_bottom_right.gif'  width='6' height='6' /></td>
				</tr>
				</table><hr size="1" /><div>
<?

	$strSql = "SELECT S.sno,P.pno,pname,sname,SUM(pqty),COUNT(*) AS CT,pgood,pnormal,pbad FROM ttlisttb AS L INNER JOIN ttstoretb AS S INNER JOIN ttprodtb AS P ON L.pno=P.pno AND S.sno=P.sno GROUP BY pname ORDER BY CT desc LIMIT 50";
	$data_rows=mysql_query($strSql,$link);
echo "<table border='0' cellpadding='0' cellspacing='1' class='profiletb' width='96%' align='center'><tr><th>排行</th><th>訂購次數</th><th>品名</th><th>邀約店家</th><th>訂購總數</th><th><img src='images/btngood.gif'></th><th><img src='images/btnnormal.gif'></th><th><img src='images/btnbad.gif'></th></tr>";
	$tt=mysql_num_rows($data_rows);

	if($tt>=3) mysql_data_seek($data_rows,3); 
	while(list($sno,$pno,$pname,$sname,$pqty,$count,$pgood,$pnormal,$pbad)=mysql_fetch_row($data_rows))
	{
		$i++;
		echo "<tr><td>$i</td><td>$count</td><td>$pname</td><td><a href='pickstore.php?sno=$sno' title='選此店家'>$sname</a></td><td>$pqty</td><td class='vote' subid='$pno'>$pgood <img src='css/images/datagrid_row_expand.gif' subid='pgood'></td><td class='vote' subid='$pno'>$pnormal <img src='css/images/datagrid_row_expand.gif' subid='pnormal'></td><td class='vote' subid='$pno'>$pbad <img src='css/images/datagrid_row_expand.gif' subid='pbad'></td></tr>";
		
	}
	echo "</table>";

      ?></div>
</td>
      <td background="images/sub01_14.gif">&nbsp;</td>
    </tr>
    <tr>
      <td width="47" height="21" valign="bottom" background="images/sub01_19.gif"><img src="images/sub01_22.gif" width="47" height="21" alt="" /></td>
      <td valign="bottom" background="images/sub01_24.gif">&nbsp;</td>
      <td width="24" height="21" valign="bottom" background="images/sub01_14.gif"><img src="images/sub01_25.gif" width="24" height="21" alt="" /></td>
    </tr>
  </table>
</div>
</body>
</html>
