<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
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
<script type="text/javascript" src="js/jquery.colorize-2.0.0.js"></script>
<script type="text/javascript" src="js/jquery.copy.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#tabsdiv').tabs();
	$('.tonus').tooltip({delay: 0, showURL: false, bodyHandler: function() {return $("<img/>").attr("src", this.src);}});
	$('.tips').tooltip();
	$(".profiletb").colorize();
	$('.stimg').click(function(){
		var st=$(this).attr('subid');
		if(st=='n')
			$(this).attr('src','css/images/tree_checkbox_1.gif').attr('subid','y').attr('alt','運作中,可以取消訂單');
		else
			$(this).attr('src','css/images/tree_checkbox_0.gif').attr('subid','n').attr('alt','已砍單,可以恢復訂單');
		var ono=$(this).attr('id');
		(st=='y')?st='n':st='y';
		$.post("changeod.php",{ono:ono,st:st}, function(ret){if(ret) alert('訂單狀態已變更');});
	});
	//copy to Clipboard
	$(".clip").click(function(){
      $.copy($(this).val());
	  //alert('已複製密碼到剪貼簿');
	  $.messager.show({title:'訊息',msg:'已複製密碼到剪貼簿',width:150});
    });

});
 
</script>
<style>
.clip{color:#FF0080;cursor:pointer;}
</style>
</head>
<body>
<div id="topdiv"><div style="width:385px;"><img src="images/version_03.jpg" width="385" height="220" alt=""></div><div style="width:385px;  position:relative; left:385px; top:-220px;"><img src="images/version_04.jpg" width="385" height="220" alt=""></div></div>
<div id="leftdiv"><div style="width:50px; height:149px; "><img src="images/sub01_01.jpg" width="250" height="149" id="getT" /></div>
<div id="menubtndiv" style="width:250px; height:490px; background-image:url(images/menubg.jpg); background-repeat:no-repeat">
<div id="bplbtndiv" class="bplbtn"><!--img src="images/bplbtn_on.gif" /--></div>
<div id="odlbtndiv" class="odlbtn"><img src="images/odlbtn_on.gif" /></div>
<div id="grlbtndiv" class="grlbtn"><!--img src="images/grlbtn_on.gif" /--></div>
<div id="ordbtndiv" class="ordbtn"><img src="images/ordbtn_on.gif" /></div>
<div id="cpnbtndiv" class="cpnbtn"><!--img src="images/cpnbtn_on.gif" /--></div>
<div id="membtndiv" class="membtn"><img src="images/membtn_on.gif" /></div>
<div id="gbbbtndiv" class="gbbbtn"><!--img src="images/gbbbtn_on.gif" /--></div>
<div id="olinkdiv"></div></div><!--? require "friendstab.php";?--></div>
<div id="newsdiv"><img src="images/title_ordchk.gif" width="432" height="44" alt="" /></div>
<div id="rightdiv">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="47" valign="top" background="images/sub01_19.gif"><img src="images/sub01_07-08.gif" width="47" height="28" alt="" /></td>
      <td valign="top" style="background-image:url(images/sub01_09.gif); background-position:top;">&nbsp;</td>
      <td width="24" height="28" valign="top" background="images/sub01_14.gif"><img src="images/sub01_10.gif" width="24" height="28" alt="" /></td>
    </tr>
    <tr>
      <td valign="top" background="images/sub01_19.gif"><img src="images/sub01_09-13.gif" width="47" height="121" alt="" /></td>
      <td align="center" valign="top"><div style="text-align:left;padding:10px; font-size:11px;color:#999999;"><ul style="list-style:inside"><li style="list-style:square;">您可以在線上直接修改或取消您的邀約單。</li><li style="list-style:square;">只有未逾期邀約才可以被修改狀態。</li><li style="list-style:square;"><img src='css/images/tree_checkbox_1.gif' />邀約進行中,可取消<img src='css/images/tree_checkbox_0.gif' />邀約暫取消,可恢復<img src='css/images/tree_checkbox_2.gif' />邀約已正常結束<img src='css/images/tree_checkbox_3.gif' />邀約取消並已過期</li></ul></div>
<div id="tabsdiv" style="width:680px;height:auto; padding-left:10px;">
<div title="以明細方式檢視訂單" style="text-align:center;padding:10px;">
  <?
$uno=$_COOKIE['teatimeuserno'];
if($uno)
{
	$strSql="SELECT ono,S.sno,sname,spic,otitle,onote,oorderkey,ocreatetime,odeadline,oonoff FROM ttordertb AS O INNER JOIN ttstoretb AS S ON O.sno=S.sno WHERE uno=$uno ORDER BY ocreatetime desc,sno";
	$data_rows=mysql_query($strSql,$link);
	$i=1;
	$tt=mysql_num_rows($data_rows);
	echo "<table border='0' cellpadding='0' cellspacing='1' class='profiletb' width='99%'><tr><th>#</th><th>邀約標題</th><th>邀約店家</th><th>終止時間</th><th>訂單密碼 <span style='color:#ff0000'>(點一下)</span></th><th>狀態</th></tr>";
	while(list($ono,$sno,$sname,$spic,$otitle,$onote,$oorderkey,$ocreatetime,$odeadline,$oonoff)=mysql_fetch_row($data_rows))
	{
		$diff=strtotime($odeadline)-strtotime(date("Y-m-d H:i:s"));
		if($diff<=0) 
		{
			if($oonoff=="y")
				$stimg="<img src='css/images/tree_checkbox_2.gif' alt='邀約已結束' />";
			else
				$stimg="<img src='css/images/tree_checkbox_3.gif' alt='邀約取消且已過期' />";
		}
		else
		{
			if($oonoff=="y")
				$stimg="<img src='css/images/tree_checkbox_1.gif' id='$ono' subid='y' class='stimg' alt='運作中,點此圖可以取消邀約' />"; 
			else
				$stimg="<img src='css/images/tree_checkbox_0.gif' id='$ono' subid='n' class='stimg' alt='暫時取消,點此圖可以變更為有效' />"; 
		}
		
		$tips="<font style=\"font-size:12px;color:#ff0000;\">起始時間: $ocreatetime <br> 終止時間: $odeadline</font>\n$onote";
		echo "<tr><td>$i</td><td><a href='ordermenu.php?ono=$ono&ok=$oorderkey' title='訂購選單'>$otitle</a></td><td><a href='orderlist.php?ono=$ono&ok=$oorderkey' title='下單列表'>$sname</a></td><td title='$tips' class='tips'>$odeadline</td><td><span title='複製密碼到剪貼簿' class='clip' value='$oorderkey'>$oorderkey</span></td><td>$stimg</td></tr>";
		$i++;
	}
	echo "</table>";
}
?>
</div>
<div title="以店家排列檢視訂單" style="text-align:center;padding:10px;">
<?

if($uno)
{
	$sn="";
	$strSql="SELECT ono,S.sno,sname,spic,otitle,onote,oorderkey,ocreatetime,odeadline,oonoff FROM ttordertb AS O INNER JOIN ttstoretb AS S ON O.sno=S.sno WHERE uno=$uno ORDER BY sno,ocreatetime desc";
	$data_rows=mysql_query($strSql,$link);
	$k=0;
	$i=0;
	$tt=mysql_num_rows($data_rows);
	while(list($ono,$sno,$sname,$spic,$otitle,$onote,$oorderkey,$ocreatetime,$odeadline,$oonoff)=mysql_fetch_row($data_rows))
	{
		if($spic=="") $spicstr="&nbsp;"; else $spicstr="<img class='tonus' src='storepic/$spic' width='20' height='20' />";
		if($sn!=$sname && $sn!="") echo "</table>";
		if($sn!=$sname || $sn=="") {
			echo "<div style='font-size:16px;font-weight:bold; padding:5px 35px; text-align:left;'><img src='css/images/tree_checkbox_2.gif' />店家:$sname</div><table border='0' cellpadding='0' cellspacing='1' class='profiletb' width='99%'><tr><th>#</th><th>邀約標題</th><th>起始時間</th><th>終止時間</th><th>訂單密碼 <span style='color:#ff0000'>(點一下)</span></th><th>狀態</th></tr>";
			$sn=$sname;
			if($i>0) $k+=$i; //tmp quantity for the store
			$i=0;
		}
		
		$diff=strtotime($odeadline)-strtotime(date("Y-m-d H:i:s"));
		if($diff<=0) 
		{
			if($oonoff=="y")
				$stimg="<img src='css/images/tree_checkbox_2.gif' alt='邀約已結束' />";
			else
				$stimg="<img src='css/images/tree_checkbox_3.gif' alt='邀約取消且已過期' />";
		}
		else
		{
			if($oonoff=="y")
				$stimg="<img src='css/images/tree_checkbox_1.gif' id='$ono' subid='y' class='stimg' alt='運作中,點此圖可以取消邀約' />"; 
			else
				$stimg="<img src='css/images/tree_checkbox_0.gif' id='$ono' subid='n' class='stimg' alt='暫時取消,點此圖可以變更為有效' />"; 
		}

		echo "<tr title='$onote' class='tips'><td>".($i+1)."</td><td><a href='orderlist.php?ono=$ono&ok=$oorderkey' title='下單列表'>$otitle</a></td><td>$ocreatetime</td><td>$odeadline</td><td><span title='複製密碼到剪貼簿' class='clip' value='$oorderkey'>$oorderkey</span></td><td>$stimg</td></tr>";
		if(($k+$i+1)==$tt || $tt==$i) echo "</table>"; //because $i initalized from 1, $tt==$i cos of only one store
		$i++;
	}
}
?>
</div>
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