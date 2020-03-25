<?
require_once "conn.php";
$tusertype=$_COOKIE['teatimeutype'];
$savebtn=$_REQUEST['savebutton'];
$mn=$_REQUEST['mn'];
$act=$_REQUEST['act'];
if($tusertype==2 && $mn!="" && $act="del")
{
	$strSql="DELETE FROM ttbooktb WHERE bno=$mn;";
	$strSql .="DELETE FROM ttreplytb WHERE bno=$mn;";
	mysqli_multi_query($link2,$strSql);
}
if($savebtn=="送出留言")
{
	$btitle=$_REQUEST['btitle'];
	$unick=$_REQUEST['unick'];
	$uemail=$_REQUEST['uemail'];
	$bcontent=$_REQUEST['bcontent'];
	$btime=date("Y-m-d H:i:s");
	
	if(!empty($btitle)&&!empty($bcontent)) 
	{

		if($tusertype==2)
		{
			$strSql="INSERT INTO ttreplytb (bno,unick,rcontent,rtime) VALUES ($mn,'$unick','$bcontent','$btime')";
		}
		else
		{
			if($unick=="") $unick="匿名";
			$strSql="INSERT INTO ttbooktb (unick,uemail,btitle,bcontent,btime) VALUES ('$unick','$uemail','$btitle','$bcontent','$btime')";
		}

		$ret=mysql_query($strSql);
		if($ret==true)
		{
			echo "<script>alert('成功留言');</script>";
			sendmail();
		}
		else
			echo "<script>alert('留言失敗，可能伺服器忙碌或資料錯誤,請稍後再試');</script>";
	}
}

function sendmail()
{
	$btitle=$_REQUEST['btitle'];
	$unick=$_REQUEST['unick'];
	$uemail=$_REQUEST['uemail'];
	$bcontent=$_REQUEST['bcontent'];
	$btime=date("Y-m-d H:i:s");
	$body = "============================== 
	留言標題: $title 
	留言人: $unick 
	E-mail: $uemail 
	終止時間: $btime 
	留言內容: $bcontent 
	============================== 
	下午茶:<a href='http://teatime.quantatw.com'> http://teatime.quantatw.com</a>";
	//$body ="aaaa"; //for testing
	
	$subject = "TeaTime [$title] 系統有新留言"; 
	$subject ="=?UTF-8?B?".base64_encode("$subject")."?=";
	$from = "adian.tu@quantatw.com"; 
	$to="adian.tu@quantatw.com";
	$cc=$email;
	$fromname="下午茶";
	//$header .="Reply-To: ".$to."\r\n";
	//$header .="Return-Path: ".$to."\r\n";
	
	//$header .="Content-Type: multipart/alternative\r\n";
	$boundary = uniqid( ""); 
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=big5\r\n";
	//$mailFrom = '=?utf-8?B?'.base64_encode("$fromname").'?='.' <$from>';
	$headers .= "From: adian.tu@quantatw.com\r\n";
	$headers .= "Content-type: multipart/mixed; boundary=\"$boundary\"\r\n"; 
	//Content-transfer-encoding: 8bit ;
	if($email) $headers .="Cc: $cc\r\n";
	$headers .= "To: $to\r\n";
	
	$ret=mail($to, $subject, $body , $headers); 
	
}

$srchtxt=$_REQUEST['srchtxt'];
if($srchtxt!="") $srchstr=" AND btitle LIKE '%$srchtxt%' ";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TeaTime 茶飲訂購系統 聊個兩句</title>
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/jquery.cleditor.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="js/common.js" type="text/javascript"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script>
<script type="text/javascript" src="js/jquery.cleditor.min.js"></script>
<script>
$(document).ready(function(){
	$("#bcontent").cleditor({width:400,height:180});
	$('#pagediv a').click(function(){
		var pageno=$(this).attr('title');
		var searchtxt=$('#searchtxt').val();
		location.href="?page="+pageno;
	});
	$('#searchbtn').click(function(){
		var searchtxt=$('#searchtxt').val();
		location.href="?srchtxt="+searchtxt;
	});
});	
</script>
<style>
.mtitle{ text-align:left; font-size:14px; font-weight:bold;}
.mcontent{  text-align:left;width:580px;}
#userprofiletb td{ border:solid 1px #999999;}
.dotline {font-size: 13px;line-height: 20px;background-image: url(images/dotline.gif);}
</style>
</head>
<body>
<div id="topdiv"><div style="width:385px;"><img src="images/pick1.jpg" width="385" height="220" alt=""></div><div style="width:385px;  position:relative; left:385px; top:-220px;"><img src="images/pick2.jpg" width="385" height="220" alt=""></div></div>
<div id="leftdiv"><div style="width:50px; height:149px; "><img src="images/sub01_01.jpg" width="250" height="149" id="getT" /></div>
<div id="newsdiv"><img src="images/title_book.gif" width="432" height="44" alt="" /></div>
<div id="rightdiv"><table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="47" valign="top" background="images/sub01_19.gif"><img src="images/sub01_07-08.gif" width="47" height="28" alt="" /></td>
      <td valign="top" style="background-image:url(images/sub01_09.gif); background-position:top;">&nbsp;</td>
      <td width="24" height="28" valign="top" background="images/sub01_14.gif"><img src="images/sub01_10.gif" width="24" height="28" alt="" /></td>
    </tr>
    <tr>
      <td valign="top" background="images/sub01_19.gif"><img src="images/sub01_09-13.gif" width="47" height="121" alt="" /></td>
      <td align="center" valign="top">
<div style="background-image:url(images/h002img_02.jpg);width:620px; height:40px; text-align:left;"><img src="images/h002img_01.jpg" alt="" width="63" height="37" />
  <div style=" width:200px;position:relative;top:-30px; left:80px;font-size:16px; font-weight:bold; ">謝謝您留下寶貴的意見!!</div>
  <div style=" width:280px;position:relative;top:-55px; left:300px; "><table border="0" align="center" cellpadding="2" cellspacing="1"><tr>
        <td width="204" align="left"><span style="text-align:center; padding-bottom:10px; "><input type="text" id="searchtxt" name="searchtxt" style="width:200px" value="<?=$srchtxt;?>" /></span></td>
        <td align="left"><span style="text-align:center; padding-bottom:10px; "><input type="button" id="searchbtn" name="searchbtn" style="width:60px" value="搜尋標題" /></span></td>
      </tr></table></div></div>
<?
$page_count=15; //每頁設定顯示筆
$strSql="SELECT count(*) FROM ttbooktb ORDER BY btime desc";
$sql_data_count=mysql_query($strSql); 
$row = mysql_fetch_array($sql_data_count); 
$messagecnt=$row[0];
$page_total=ceil($messagecnt/$page_count);
if (isset($_REQUEST['page'])&& $_REQUEST['page']!="") $page=intval($_REQUEST['page']); else $page=1;  //GET取得page頁數，若沒有則跑第1頁
$move=$page_count * ($page - 1); //資料移動筆數
$strSql="SELECT * FROM ttbooktb WHERE 1 $srchstr ORDER BY btime desc";
$sql_data_move=mysql_query("$strSql LIMIT $move,$page_count"); 
$tt=mysql_num_rows($sql_data_move);
while(list($bno,$unick,$uemail,$btitle,$bcontent,$btime)=mysql_fetch_row($sql_data_move))
{
	$strSql="SELECT upic FROM ttusertb WHERE unick='$unick'";
	$result3=mysql_query($strSql);
	if(list($upic)=mysql_fetch_row($result3)) $upicstr="userpic/$upic"; else $upicstr="userpic/nophoto.png";
?><div style="height:2px;"></div><table cellspacing='0' cellpadding='0' align='center' border='0' class="mcontent">
<tr>
  <td width='6' height='6'><img src='images/new_box02_top_left.gif'  width='6' height='6' /></td>
  <td background='images/new_box02_top_bg.gif'><img src='images/blank.gif'  width='1' height='1' /></td>
  <td width='6' height='6'><img src='images/new_box02_top_right.gif'  width='6' height='6' /></td>
</tr>
<tr><td background='images/new_box02_left_bg.gif'></td>
<td ><!--start message--><div class="mtitle">標題:<?=$btitle;?></div>
<div class="mcontent">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td align="center"><?=$unick;?>留言</td>
      <td bgcolor="#F2F2F9"><img src="images/text.gif" width="15" height="15" />內容: (<?=$btime;?>)</td>
    </tr>
    <tr>
      <td width="100" rowspan="2" align="center" valign="top"><img src="<?=$upicstr;?>" width="50" height="50" /><br /><? if($tusertype==2) echo "<a href='?mn=$bno'>回覆</a> | <a href='?mn=$bno&act=del'>刪除</a>";?></td>
      <td bgcolor="#F2F2F9" align="center"><div class="dotline" style="text-align:left;width:90%;padding:3px;color:#295454;"><?=$bcontent;?></div></td>
    </tr>
    <tr>
      <td><div style="height:5px;"></div>
<?
	$strSql="SELECT * FROM ttreplytb WHERE bno=$bno";
	$result2=mysql_query($strSql);
	while(list($rno,$bno,$ruser,$rcontent,$rtime)=mysql_fetch_row($result2))
	{
		$strSql="SELECT upic FROM ttusertb WHERE unick='$ruser'";
		$result3=mysql_query($strSql);
		if(list($upic)=mysql_fetch_row($result3)) $upicstr="userpic/$upic"; else $upicstr="userpic/nophoto.png";
?>
      <table width="100%" border="0" cellpadding="2" cellspacing="2" bgcolor="#ECF1FB">
        <tr>
          <td align="center"><?=$ruser;?>回覆</td>
          <td><img src="images/reply.gif" width="18" height="18" />內容: <img src="images/email.gif" width="15" height="15" /> <?=$uemail;?></td>
          </tr>
        <tr>
          <td width="80" align="center"><img src="<?=$upicstr;?>" width="50" height="50" /></td>
          <td valign="top" class="dotline"><span style="color:#5353A6;"><?=$rcontent;?></span></td>
          </tr>
        <tr>
          <td>&nbsp;</td>
          <td align="right">日期:<?=$rtime;?></td>
          </tr>
      </table>
<?
//for inner loop
}
?></td>
    </tr>
  </table>
</div><!--end message-->
</td>
  <td background='images/new_box02_right_bg.gif'></td>
</tr>
<tr>
  <td><img src='images/new_box02_bottom_left.gif'  width='6' height='6' /></td>
  <td background='images/new_box02_bottom_bg.gif'><img src='images/blank.gif'  width='1' height='1' /></td>
  <td><img src='images/new_box02_bottom_right.gif'  width='6' height='6' /></td>
</tr>
</table>
<?
//for outter loop
}

function showpages($page,$page_total)
{
	
	for($k=1;$k<=$page_total;$k++)
	{
		if($page==$k)
			$ps .=" <a href='javascript:void(0);' title='$k' style='color:#ffffff;background-color:#69f'>$k</a> ";
		else
			$ps .=" <a href='javascript:void(0);' title='$k' style='border:solid 1px #cccccc;'>$k</a> ";
	}
	return $ps;
}

if($tt>0)
{
	if(($page-1)<1) $prevpage=1; else $prevpage=$page-1;
	if(($page+1)>$page_total) $nextpage=$page_total; else $nextpage=$page+1;
	echo "<div id='pagediv' style='margin:20px;text-align:center;'>【<a href='javascript:void(0);' title='1'>最前</a>】【<a href='javascript:void(0);' title='".$prevpage."'>上一頁</a>】".showpages($page,$page_total)."【<a href='javascript:void(0);' title='".$nextpage."'>下一頁</a>】【<a href='javascript:void(0);' title='$page_total'>最後</a>】(共 $tt 筆)</div>";
}
else
	echo "<div style='text-align:center'>...沒有留言資料...</div>";
 
?>
<div style="width:550px; border:#693 dashed 1px;padding:10px;">
<form>
<table border="0" cellspacing="1" cellpadding="2" style=" text-align:left;" id="userprofiletb">
  <tr>
    <td>姓名/暱稱:</td>
    <td><input name="unick" type="text" id="unick" style="FONT-SIZE: 11px;" value="<?=$_COOKIE['teatimeunick'];?>" size="30" /></td>
    </tr>
  <tr>
    <td>E-MAIL:</td>
    <td><input name="uemail" type="text" id="uemail" style="FONT-SIZE: 11px;" value="<?=$_COOKIE['teatimeuuser'];?>" size="40" /></td>
    </tr>
  <tr>
    <td>標題:</td>
    <td> <input style="FONT-SIZE: 11px;" name="btitle" type="text" id="btitle" size="50" value="<? if($tusertype==2) echo "Re:管理員回覆";?>" /><input type="hidden" id="mn" name="mn" value="<?=$mn;?>" /></td>
    </tr>
  <tr>
    <td>留言內容:</td>
    <td><textarea name="bcontent" cols="50" rows="6" id="bcontent"></textarea></td>
    </tr>
  <tr>
    <td colspan="2" align="center"><input style="width:80px;height:40px;"  type="submit" value="送出留言" name="savebutton" />
    </td>
    </tr>
</table></form>
</div></td>
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
