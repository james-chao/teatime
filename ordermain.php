<?
header( 'Content-Type: text/html; charset=utf-8' );
include_once(dirname(__FILE__) . '/Classes/utility.php');

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";

$ono=$_REQUEST["ono"];

if(!empty($ono))
{
	$strSql = "SELECT * FROM ttordertb WHERE ono=$ono";
	$data_rows=mysql_query($strSql);
	
	list($ono,$sno,$uno,$unick,$uemail,$pnos,$otitle,$onote,$odeadline,$operiod,$oorderkey,$oonoff,$omailflag,$ocreatetime)=mysql_fetch_row($data_rows);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TeaTime 茶飲訂購系統 訂單維護</title>
<head>
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.tooltip.css" />
<link rel="stylesheet" type="text/css" href="css/jquery.cleditor.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="js/jquery.tooltip.js" type="text/javascript"></script>
<script type="text/javascript" src="js/jquery.cleditor.min.js"></script>
<script type="text/javascript" src='js/jquery.autocomplete.js'></script>
<script>
$(function(){
	$('#sendout').click(function(){
		 var cnt=0;
		$('input[name=cycle]').each(function(idx,val){
			if($(this).attr('checked')==true) cnt++;
		});
		
		if(cnt==0) {alert('請選一種周期方式!');return false;}
		return true;
	});
});
</script>
<style>
#profiletb td{ border:solid 1px #999999; background-color:#FFFFFF;}
#friendlist{border:solid 1px #0099FF;width:500px;height:150px; overflow:scroll; padding:2px;padding-top:0px;}
#profiletb td{ border:solid 1px #999999;}
.friendpic{width:40px;height:40px;float:left;margin:1px;border:1px solid #eee;cursor:pointer}
.divcenter{text-align:center;}
</style>
</head><body>
<center>
<form name="form1" id="form1" method="post" action="updateorder.php">
<div id="tabsdiv">
<div title="2.填寫邀約訂單" style="text-align:center;padding:10px;">
  <table  border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#666666">
    <tbody>
      <tr>
        <td height="40" colspan="2" align="center" bgcolor="#FFFFCC"><strong>邀約資訊</strong></td>
      </tr>
      <tr>
        <td height="30" align="right" bgcolor="#FFFFFF">標題:</td>
        <td align="left" bgcolor="#FFFFFF"><input name="ordertitle" class="easyui-validatebox"  id="ordertitle" value="<?=$otitle;?>" size="60" required="true" />
          <span style="text-align:center; padding-bottom:10px; ">
          <input name="ono" type="hidden" id="ono" value="<?=$ono;?>" /><input name="oorderkey" type="hidden" id="oorderkey" value="<?=$oorderkey;?>" />
        </span></td>
      </tr>
      <tr>
        <td height="30" align="right" bgcolor="#FFFFFF">發起人:</td>
        <td align="left" bgcolor="#FFFFFF"><input  maxlength="20" size="20" name="nickname" id="nickname" value="<?=$unick;?>" /></td>
      </tr>
      <tr>
                <td height="30" align="right" bgcolor="#FFFFFF">受邀人:</td>
                <td align="left" bgcolor="#FFFFFF"><textarea name="ufname" id="ufname" style="font-size:11px;width:400px;height:40px;"><?php

$strSql="SELECT ufname FROM ttinvitetb WHERE ono=$ono";
$util->db->Query($strSql);
while(!$util->db->EndOfSeek())
{
	$row=$util->db->RowArray();
	echo $row['ufname'].",";	
}
?></textarea>&nbsp;&nbsp;&nbsp;<input  type="checkbox" name="flchkbox" id="flchkbox" value="1" />從朋友清單加入<br /><span style='font-size:12px;color:#666'>PS.請填公司群組或個人郵件，如:someone (某米人)<span style="color:#00F">,</span>*******總經理室<span style="color:#F00">;</span>John.O(醬喔)</span>
                  </td>
              </tr>
              <tr id="fltr" style='display:none;'>
                <td height="30" align="right" bgcolor="#FFFFFF">我的朋友:</td>
                <td align="left" bgcolor="#FFFFFF"><a href="javascript:void(0);" id="flclose" ><img alt='朋友列表' title='朋友列表' border="0" src='css/images/tabs_close.gif' />開啟 / 隱藏</a><input name="ufemail" id="ufemail" type="hidden" /><div  id="friendlist"><?php

$strSql="SELECT U.uuser,uname,unick,upic,ufno FROM ttfriendtb F INNER JOIN ttusertb U ON F.ufno=U.uno WHERE uonoff='y' AND fagree='y' AND F.uno=$uno UNION SELECT U.uno,uname,unick,upic,ufno FROM ttfriendtb F INNER JOIN ttusertb U ON F.uno=U.uno WHERE uonoff='y' AND fagree='y' AND F.ufno=$uno";


$util->db->Query($strSql);
while(!$util->db->EndOfSeek())
{
	$imgsrc=$util->picurl."quanta_".$upic."_MThumb.jpg";
	if(!$util->isPhotoExist($imgsrc)) $imgsrc="userpic/nophoto.png";
	$user=$util->db->RowArray();
	echo "<div class='friendpic'><div class='divcenter'><img title='邀請 ".$user['unick']."' src='$imgsrc' width='25' height='25' class='add2buy' ufemail='".$user['uuser']."' ufnick='".$user['unick']."' /></div><div class='divcenter'>".$user['uname']."</div></div>";
}
?></div></td>
              </tr>
      <tr align="middle">
        <td height="30" align="right" bgcolor="#FFFFFF">終止時間:</td>
        <td align="left" bgcolor="#FFFFFF"><input name="deadline" id="deadline" style="width:160px" value="<?=$odeadline;?>" required="true" />
          (24小時制: yyyy-mm-dd 例: 2010-04-01 23:11:13)</td>
      </tr>
      <tr>
                <td height="30" align="right" bgcolor="#FFFFFF">循環周期:</td>
                <td align="left" bgcolor="#FFFFFF"> <input type="radio" name="cycle" id="byone" value="byone" /><label id="byonelb" style="color:#F00" for="byone">單次邀約</label><input type="radio" name="cycle" id="bydays" value="bydays" />每<input  style="width:25px" name="duration" id="duration" />天 / <input type="radio" name="cycle" value="byweeks" id="byweeks"/>每星期<input  type="checkbox" name="week[]" id="week[]" value="0" />日<input  type="checkbox" name="week[]" id="week[]" value="1" />一<input  type="checkbox" name="week[]" id="week[]" value="2" />二<input  type="checkbox" name="week[]" id="week[]" value="3" />三<input  type="checkbox" name="week[]" id="week[]" value="4" />四<input  type="checkbox" name="week[]" id="week[]" value="5" />五<input type="checkbox" name="week[]" id="week[]" value="6" />六</td>
              </tr>
      <tr align="middle">
        <td height="30" align="right" bgcolor="#FFFFFF">邀約說明:</td>
        <td align="left" bgcolor="#FFFFFF"><textarea name="ordernote" cols="70" rows="12"  id="ordernote"><?=$onote;?></textarea></td>
      </tr>
      <tr align="middle">
        <td height="30" align="right" bgcolor="#FFFFFF">E-Mail:</td>
        <td align="left" bgcolor="#FFFFFF"><input  maxlength="40" size="40" name="useremail" id="useremail" value="<?=$uemail;?>" class="easyui-validatebox" validType="email"></ />
          (結單後自動寄信用)</td>
      </tr>
      <tr align="middle">
        <td height="30" colspan="2" align="center" bgcolor="#FFFFFF"><input style="width:80px;height:40px;"  type="submit" value="修改邀約" name="Submit" id="sendout" /></td>
      </tr>
    </tbody>
  </table>
    </div>
<div title="1.選擇商品" style="text-align:center;padding:10px;" id="storeproduct">
<?
if($sno)
{
	$cn="";
	$strSql="SELECT pno,pname,pprice,ppic,psize,ptemp,ptaste,prater,cname FROM ttprodtb AS P INNER JOIN ttcatetb AS C ON P.sno=C.sno AND P.cno=C.cno WHERE P.sno=$sno ORDER BY P.sno,C.cno";
	$data_rows=mysql_query($strSql,$link);
	$tt=mysql_num_rows($data_rows);
	$k=0;
	echo "<div id='selpddiv' style='font-size:12px;'></div>";
	while(list($pno,$pname,$pprice,$ppic,$psize,$ptemp,$ptaste,$prater,$cname)=mysql_fetch_row($data_rows))
	{
		$pricestr .=$pprice."-";
		if($ppic=="") $ppicstr="&nbsp;"; else $ppicstr="<img class='tonus' src='prodpic/$ppic' width='20' height='20' />";
		if(($cn!=$cname && $cn!="")) echo "</table></div>";
		if($cn!=$cname || $cn=="") 
		{
			echo "<div class='pdincate$k'><div style='font-size:14px;font-weight:bold; padding:5px 35px; text-align:left;'><img src='css/images/tree_checkbox_2.gif' />系列:$cname</div><table border='0' cellpadding='0' cellspacing='1' id='profiletb' width='99%'><tr><th title='點此全選本系列'><input class='checkcate' type='checkbox' value='$k' ></th><th>品名</th><th>大小</th><th>單價</th><th>冷熱</th><th>甜淡</th><th>圖片</th></tr>";
			$cn=$cname;
		}
		$selchk="";
		if(strpos(",$pnos,",$pno)) $selchk="checked";
		echo "<tr><td><input id='pno[]' name='pno[]' type='checkbox' value='$pno' class='$pprice' $selchk /></td><td title='$pintro'>$pname</td><td>$psize</td><td>$pprice</td><td>$ptemp</td><td>$ptaste</td><td>$ppicstr</td></tr>";
		$k++;
		if($k==$tt) echo "</table></div>";
	}
	$pricearr=explode("-",$pricestr);
	sort($pricearr);
	$ppchkbox="<input class='checkall' type='checkbox' >全選 ";
	$tmppp="";
	foreach($pricearr as $pp)
	{
		if($pp!=="" && strpos("$tmppp","$pp")<=0) 
		{
			$ppchkbox .="<input class='ppsel' value='$pp' type='checkbox'>$pp 元 ";
			$tmppp .="-".$pp;
		}
	}
	
}
?></div>
</div></form>
</center></body></html>
<script>
$(document).ready(function(){
	$('#selpddiv').html("<?=$ppchkbox;?>");
	$('.ppsel').click(function(){
		var flg=$(this).attr('checked');
		var val=$(this).val();
		if(flg)
		{
			$('#storeproduct input[class*='+val+']').attr('checked',true);
		}
		else
		{
			$('#storeproduct input[class*='+val+']').attr('checked',false);
		}
	});
	$('td:empty').html('&nbsp;');
	$('.tonus').tooltip({
		delay: 0, showURL: false, bodyHandler: function() {
		return $('<img/>').attr('src', this.src);}
	});
	$('.checkall').click(function(){
		var flg=$(this).attr('checked');
		if(flg)
			$('#storeproduct input:checkbox').attr('checked',true);
		else
			$('#storeproduct input:checkbox').attr('checked',false);
	});
	$('.checkcate').click(function(){
		var chk=$(this).val();
		var flg=$(this).attr('checked');
		if(flg)
			$('.pdincate'+chk+' input:checkbox').attr('checked',true);
		else
			$('.pdincate'+chk+' input:checkbox').attr('checked',false);
	});
	$("#ordernote").cleditor()[0].focus();
	
	$('input[id^=week],#byweeks').click(function(){$('#byweeks').attr('checked',true);$('#duration').val('');});
	$('#duration').keyup(function(){
		$('#bydays').attr('checked',true);
		$('input[id^=week]').attr('checked',false);
		if($(this).val()!="" && !IsNumeric($(this).val())) {alert('請不要輸入非數字的字元!'); $(this).val('');}
	});
	$('#bydays').click(function(){$('#duration').keyup()});
	
	$('#byone,#byonelb').click(function(){
		$('#duration').val('');
		$('input[id^=week]').attr('checked',false);
	});
	$('#form1').submit(function(){
		if($('#bydays').attr('checked') && $('#duration').val()=="" ) alert('您尚未填入所選的周期數值');
		return $(this).form('validate');
	}); 
	
	var period="<?=$operiod;?>";
	if(period!="")
	{
		var periodarr=period.split("#");
		var pdtyp=periodarr[0];
		var pdval=periodarr[1]; 
		
		switch(pdtyp)
		{
			case "none":
				$('#byone').attr("checked",true);
				break;
			case "days": 
				$('#bydays').attr("checked",true);
				$('#duration').val(pdval);
				break;
			case "weeks":
				$('#byweeks').attr("checked",true);
				$('input[id^=week]').filter(function(index) {
                    return pdval.indexOf($(this).val())>-1;
                }).attr("checked",true);
				break;
		}
	}
	$('#flchkbox').click(function(){
		$('#fltr').slideToggle();
	});
	$('#flclose').click(function(){
		$('#friendlist').slideToggle();
	});
		$('.add2buy').click(function(){
		var ufemail=$(this).attr('ufemail');
		var ufnick=$(this).attr('ufnick');
		
		if($('#ufemail').val().indexOf(ufemail)==-1)
		{
			$('#ufname').val($('#ufname').val()+ufnick+";");
			$('#ufemail').val($('#ufemail').val()+ufemail+";");
		}
	});
	$('#ufname').autocomplete({
			delimiter: /(,|;)\s*/,
			serviceUrl:"getmaillist2.php",
			onSelect: function (suggestion,data) {
				$('#ufemail').val($('#ufemail').val()+data+";");	
			}
	});
});
</script>