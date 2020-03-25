<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";

?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	
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
	$('.editorder').click(function(){
		var ono=$(this).attr('subid');
		
		$.post("ordermain.php",{ono:ono},function(data){$('#fundiv').html(data);});
	});
	$('.profiletb tr:even').css('background-color','#FFFFDC');
	$('.profiletb tr:odd').css('background-color','#FFFFFF');
});
 
</script>
<div style="text-align:left;padding:10px; font-size:11px;color:#999999;"><ul style="list-style:inside"><li style="list-style:square;">您可以在線上直接修改或取消您的邀約單。</li><li style="list-style:square;">只有未逾期邀約才可以重新編輯及修改狀態。</li>
<li style="list-style:square;"><img src='css/images/tree_checkbox_1.gif' />邀約進行中,可修改<img src='css/images/tree_checkbox_0.gif' />邀約暫取消,可修改<img src='css/images/tree_checkbox_2.gif' />邀約已正常結束,不可改<img src='css/images/tree_checkbox_3.gif' />邀約取消並已過期,不可改</li></ul></div>
<div id="tabsdiv" style="width:680px;height:auto; padding-left:10px;">
  <?
$uno=$_COOKIE['teatimeuserno'];
if($uno)
{
	$strSql="SELECT ono,S.sno,sname,spic,otitle,onote,oorderkey,ocreatetime,odeadline,oonoff FROM ttordertb AS O INNER JOIN ttstoretb AS S ON O.sno=S.sno WHERE uno=$uno ORDER BY ocreatetime desc,sno";
	$data_rows=mysql_query($strSql,$link);
	$i=1;
	$tt=mysql_num_rows($data_rows);
	echo "<table border='0' cellpadding='0' cellspacing='1' class='profiletb' width='99%'><tr><th>#</th><th>邀約標題</th><th>邀約店家</th><th>終止時間</th><th>訂單密碼</th><th>狀態</th></tr>";
	while(list($ono,$sno,$sname,$spic,$otitle,$onote,$oorderkey,$ocreatetime,$odeadline,$oonoff)=mysql_fetch_row($data_rows))
	{
		$diff=strtotime($odeadline)-strtotime(date("Y-m-d H:i:s"));
		if($diff<=0) 
		{
			if($oonoff=="y")
				$stimg="<img src='css/images/tree_checkbox_2.gif' alt='邀約已結束' />";
			else
				$stimg="<img src='css/images/tree_checkbox_3.gif' alt='邀約取消且已過期' />";
			$title=$otitle;
		}
		else
		{
			if($oonoff=="y")
				$stimg="<img src='css/images/tree_checkbox_1.gif' id='$ono' subid='y' class='stimg' alt='運作中,點此圖可以取消邀約' />"; 
			else
				$stimg="<img src='css/images/tree_checkbox_0.gif' id='$ono' subid='n' class='stimg' alt='暫時取消,點此圖可以變更為有效' />"; 
			$title="<a class='editorder' href='javascript:void(0);' title='編輯邀約' subid='$ono'>$otitle</a>";
		}
		
		echo "<tr><td>$i</td><td>$title</td><td>$sname</td><td>$odeadline</td><td>$oorderkey</td><td>$stimg</td></tr>";
		$i++;
	}
	echo "</table>";
}
?>
</div>