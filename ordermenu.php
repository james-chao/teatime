<?
session_start();
//for user reorder
session_unset(); 
session_destroy();
header( 'Content-Type: text/html; charset=utf-8' );
include_once(dirname(__FILE__) . '/Classes/utility.php');

if($_REQUEST['ono'])
{
	$oderkey=$_REQUEST['ok'];
	$orderno=$_REQUEST['ono'];
	
	if($util->userdata){
		$uuser=$util->userdata['uuser'];
		$unick=$util->userdata['unick'];
		$utype=$util->userdata['utype'];
		$uno=$util->userdata['uno'];
		//owner
		$table="ttordertb";
		$where=array(
			'ono'=>$util->db->SQLValue($orderno,'int'),
			'unick'=>$util->db->SQLValue($unick)
		);
		$ret=$util->db->SelectRows($table,$where);
		if($ret) $owner=$util->db->RowArray();
		//recipts
		$table="ttinvitetb";
		$where=array(
			'ono'=>$util->db->SQLValue($orderno,'int'),
			'ufname'=>$util->db->SQLValue($unick)
		);
		
		$ret=$util->db->SelectRows($table,$where);
		if($ret) $invite=$util->db->RowArray();
	}
	
	//if($owner || $invite)
	//{		
		$epxtime=365*24*60*60;
		setcookie("teatimeorderkey",$oderkey,time()+$epxtime);
		setcookie("teatimeorderno",$orderno,time()+$epxtime);
	//}
	//else
		//echo "<script language='javascript'>alert('請確定您已在本訂購的邀約名單中！');location.href='/';</script>";
}
else
{
	$oderkey=$_COOKIE['teatimeorderkey']; 
	$orderno=$_COOKIE['teatimeorderno'];
}
if ($oderkey=="" || $orderno==""){
	echo "<script language='javascript'>alert('請確定您已取得正確的訂單碼！');location.href='/';</script>";
}
require_once "conn.php";

$pnoarr=array();
function prodlist($rows)
{
	global $pnoarr;
	list($pno,$pname,$pprice,$pintro,$ppic,$psize,$ptaste,$ptemp,$cname,$psuit)=$rows;
	$pnoarr[]=$pno;
	$selsize=explode("-",$psize);
	$selprice=explode("-",$pprice);
	$seltaste=explode("-",$ptaste);
	$seltemp=explode("-",$ptemp);
	foreach($selsize as $sz)
	{
		$szstr .="<option>$sz</option>";
	}
	/*foreach($selprice as $pp)
	{
		$ppstr .="<option>$pp</option>";
	}*/
	foreach($seltaste as $ts)
	{
		$tsstr .="<option>$ts</option>";
	}
	foreach($seltemp as $tp)
	{
		$tpstr .="<option>$tp</option>";
	}
	
	if($ppic)
		$tips="$pname<br><img src=prodpic/$ppic /><br>說明:<font color=#0000ff>$pintro</font><br>功效:<font color=#ff0000>$psuit</font>";
	else
		$tips="$pname<br>說明:<font color=#0000ff>$pintro</font><br>功效:<font color=#ff0000>$psuit</font>";
	$newprice=($selprice[0]=="")? 0:$selprice[0]; //once didn't maintatin the price
	echo "<tr class='prodtr' title='$tips'><td>$pname</td><td class='ppricetd'><input type='text' id='pprice[]' name='pprice[]' value='$newprice' class='pprice' title='單價會依選擇的大小不同而變化' readonly='readonly' /><input type='hidden' id='pp' name='pp' value='$pprice'></td><td><select name='psize[]' id='psize[]'>$szstr</select><td><select name='ptemp[]' id='ptemp[]'>$tpstr</select></td><td><select name='ptaste[]' id='ptaste[]'>$tsstr</select></td><td><input class='pqty' name='pqty[]' type='text' id='pqty[]' style='width:15px' /></td></tr>";
 
}


if($_REQUEST['ok'] && $_REQUEST['ono'])
{
	$oderkey=$_REQUEST['ok'];
	$orderno=$_REQUEST['ono'];
}
else
{
	$oderkey=$_COOKIE['teatimeorderkey'];
	$orderno=$_COOKIE['teatimeorderno'];
}

$strSql="SELECT pnos,otitle,onote,odeadline,sname,stel,sfax,saddr,spic,surl,ocreatetime,operiod,oonoff,scity,scounty FROM ttordertb AS O INNER JOIN ttstoretb AS S ON O.sno=S.sno WHERE ono=$orderno AND oorderkey='$oderkey'";

if($_COOKIE['teatimeuuser']=="" && $_COOKIE['teatimeutype']=="")
	$strSql .=" AND oonoff='y'";  //訪客只能秀有運作的單
	
$data_rows=mysql_query($strSql,$link);
if($rows = mysql_fetch_row($data_rows))
{
	$pnos=$rows[0];
	$otitle=$rows[1];
	$onote=$rows[2];
	$odeadline=$rows[3];
	$oddline=explode(" ",$odeadline);
	$ymdstr=explode("-",$oddline[0]);
	$hmsstr=str_replace(":",",",$oddline[1]);
	
	$sname=$rows[4];
	$stel=$rows[5];
	$sfax=$rows[6];
	$scity=$rows[13];
	$scounty=$rows[14];;
	$saddr=$rows[7];
	$spic=$rows[8];
	$surl=$rows[9];
	$ocreatetime=$rows[10];
	$operiod=$rows[11];
	$oonoff=$rows[12];
	
	$periodarr=explode("#",$operiod);
	$pdtyp=$periodarr[0];
	$pdval=$periodarr[1]; 
	$now=date("Y-m-d H:i:s");
	switch($pdtyp)
	{
		case "none":
			$inputonoff=0; //menu:on
			$jsdeadline=$ymdstr[0].",".($ymdstr[1]-1).",".$ymdstr[2].",".$hmsstr;
			break;
		case "days": 
			$sndiff=dateDiff($ocreatetime,$now); //create time to now
			$endiff=dateDiff($now,$odeadline); //now to deadline
			if($endiff>=0 && $pdval>0 && ($sndiff%$pdval)==0)
			{
				$jsdeadline=date("Y").",".(date("m")-1).",".date("d").",".$hmsstr;
				$inputonoff=0; //0:menu on, 1:menu off

			}
			else
			{
				$inputonoff=1; 
			}
			break;
		case "weeks":
			$endiff=dateDiff($now,$odeadline); //now to deadline
			if($endiff>=0 && in_array(date("w",strtotime($now)),explode("-",$pdval)))
			{
				$jsdeadline=date("Y").",".(date("m")-1).",".date("d").",".$hmsstr;
				$inputonoff=0; //0:menu on 1:menu off
			}
			else
			{
				$inputonoff=1; 
			}
			break;
	}

	if($surl)
		$storedata="店名:$sname<br />電話:$stel<br /><a href='$surl' target='_blank'><img alt='Fax:$sfax\n$sintro' src='storepic/$spic' width='110' height='70' /></a><br />$scity$scounty$saddr";
	else
		$storedata="店名:$sname<br />電話:$stel<br /><img alt='Fax:$sfax\n$sintro' src='storepic/$spic' width='110' height='70' /><br />$scity$scounty$saddr";
}

function dateDiff($startTime, $endTime) {
    $start = strtotime(date("Y-m-d",strtotime($startTime))); //just Y-m-d, no h:i:s
    $end = strtotime(date("Y-m-d",strtotime($endTime)));
    $timeDiff = $end - $start;
    return floor($timeDiff / (60 * 60 * 24));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TeaTime 茶飲訂購系統 邀約選單</title>
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/easyui.css" />
<link rel="stylesheet" type="text/css" href="css/icon.css" />
<link rel="stylesheet" href="css/jquery.tooltip.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="js/jquery.easyui.min.js" type="text/javascript"></script>
<script src="js/easyui-lang-zh_TW.js" type="text/javascript"></script>
<script src="js/common.js" type="text/javascript"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script>
<script src="js/jquery.tooltip.js" type="text/javascript"></script>
<script src="js/jquery.countdown.min.js" type="text/javascript"></script>
<script src="js/jquery.countdown-zh-TW.js" type="text/javascript"></script>
<script src="js/jquery.masonry.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(function(){
	$('.prodtr').tooltip();
	$('.prodtr select[id="psize[]"]').click(function(){
		var seled=$(this).find(":selected").index(); 
		var theppstr=$(this).parent().parent().children('.ppricetd').children(':nth-child(2)').val();
		var newval=theppstr.split("-");
		$(this).parent().parent().children('.ppricetd').children(':nth-child(1)').val(newval[seled]);
	});
	$('#countdown').countdown({
		until: new Date(<?=$jsdeadline;?>),
		layout: '{dn}{dl} {hn}{hl} {mn}{ml} {sn}{sl}',
		expiryText:'本日無邀約或已結束'
	}); 
	if($('#countdown').text()=="0天 0時 0分 0秒" || <?=$inputonoff;?> || <? echo ($oonoff=="y")?'0':'1';?>) 
	{//while deadline,not on cycle, order off
		$('#countdown').countdown('destroy'); 
		$('#countdown').text("本日無邀約或已結束");
		$('.userarea input').attr("disabled",true);
	}
	
	$('.pqty').keyup(function(){
								
		if($(this).val() != "") {      
			var value = $(this).val().replace(/^\s\s*/, '').replace(/\s\s*$/, '');      
			var intRegex = /^\d+$/;      
			if(!intRegex.test(value)) 
			{         
				alert("數量請輸入整數!");
				$(this).val("");  
			} 
		}
	});
	$('#sidemenubtn img,#sidemenubtn a').click(function(){
		var wh=$(this).attr('title');
		
		if(wh=="開啟選單")
		{
			$('#sidemenu').slideDown(500);
			$(this).attr('title','隱藏選單');
			$('#sidemenubtn img').attr('src','css/images/panel_tool_collapse.gif');
		}
		else
		{
			$('#sidemenu').fadeOut(2000);
			$(this).attr('title','開啟選單');
			$('#sidemenubtn img').attr('src','css/images/panel_tool_expand.gif');
		}
	});
	$('#ordermenu').masonry({
        itemSelector: '.box',
		 columnWidth: 1
	});
	$('#form1').submit(function(){
		var tt=0;
		$('.pqty').each(function(){
			if($(this).val()!="") tt++;
		});
		if(tt==0) {alert('請輸入訂購數量');return false;}
				
		if($('#username').val()==''){
			alert('請填寫訂購人');
			return false;
		}
	});
});

</script>
<style>
#maindivleft{ position:absolute;background-color:#fff; width:302px; height:288px; margin-left:50%; left:-510px;}
#maindivbtn{ position:absolute;background-color:#fff; width:458px; height:288px; margin-left:50%; left:-208px;}
#maindivright{ position:absolute;background-color:#fff; width:260px; height:288px; margin-left:50%; left:250px; background-image:url(images/menu_rightbg.jpg); background-repeat:no-repeat;}
#maindivbar{ position:absolute;background-color:#fff; width:1020px; height:50px; margin-left:50%; top:275px; left:-510px; background-image:url(images/menubar_bg.jpg); background-repeat:no-repeat; background-position:center top;padding-top:20px;}
#maindivbody{ position:absolute;background-color:#fff; width:1020px; height:auto; margin-left:50%; top:329px; left:-510px;}
.newpdtd td,th{ border:solid 1px #999999;}
.prodlist th,td{text-align:center}
.prodtr td{ background-color:#f6f6f6;}
#tabsdiv li{list-style:square; }
.pprice{width:26px;border:#f6f6f6 solid 1px; background-color:#f6f6f6;}
.box {FLOAT:left; margin:1px;}
.wrap {CLEAR: both;PADDING-BOTTOM:10px; PADDING-TOP:10px;}
.ordtitle{
	font-weight:bold;
	font-size:20px;
	color:#F09; 
	/*text-shadow:1px 1px 3px #FF0000;*/
	text-shadow: 0 0 0, 1px 1px 3px rgba(0,0,0,0.5); 
	font-family:微軟正黑體,標楷體,新細明體;
	/*behavior: url(plugin/ie-css3.htc);*/
}

</style>
</head>

<body><div id="maindivleft"><table width="302" border="0" cellspacing="0" cellpadding="0">
    <tr><td><img src="images/main_1.jpg" width="302" height="127" alt="" /></td></tr>
    <tr><td><img src="images/main_4.jpg" width="302" height="161" alt="" /></td></tr></table></div>
<div id="maindivbtn">
<div style="background-image:url(images/main_menubg.jpg); background-repeat:no-repeat; width:458px; height:288px;">
<div id="odlbtndiv2" class="odlbtn"><img src="images/odlbtn_on.gif" /></div>
<div id="cpnbtndiv2" class="cpnbtn"><!--img src="images/cpnbtn_on.gif" /--></div>
<div id="grlbtndiv2" class="grlbtn"><!--img src="images/grlbtn_on.gif" /--></div>
<div id="ordbtndiv2" class="ordbtn"><img src="images/ordbtn_on.gif" /></div>
<div id="bplbtndiv2" class="bplbtn"><!--img src="images/bplbtn_on.gif" /--></div>
<div id="gbbbtndiv2" class="gbbbtn"><!--img src="images/gbbbtn_on.gif" /--></div>
<div id="membtndiv2" class="membtn"><img src="images/membtn_on.gif" /></div>
<div id="msgdiv"><marquee width=260 height=102 direction=up scrollamount=1 scrolldelay=5 onmouseout="this.start()"  onmouseover="this.stop()"><span style='font-size:12px'><?=$onote;?></span></marquee></div>
  </div></div>
<div id="maindivright">
  <div id="sidemenubtn" style="padding-top:10px;font-size:12px;color:#0000ff;font-weight:bold; cursor:pointer;"><img  title="開啟選單" src="css/images/panel_tool_expand.gif" /><a href="javascript:void(0)" title="開啟選單">功能選單</a></div><div style="background-image:url(images/sidemenubg.gif); background-repeat:no-repeat;display:none;" id="sidemenu"><div style="padding-top:50px;"></div><div style="width:260px; height:260px; font-size:12px;padding-left:50px; line-height:15px" id="olinkdiv2"></div></div><div style="font-size:12px;color:#999999;position:relative;top:120px">
    <?=$storedata;?>
</div></div>
<div id="maindivbar"><table width="980" align="right" cellpadding="0" cellspacing="0"><tr><td width="25%"></td><td><div class="ordtitle"><?=mb_substr(strip_tags($otitle), 0, 22, 'UTF-8');?><? if(strlen(strip_tags($otitle))>22) echo "...";?></div></td><td style='width:25%;align:right;'>本次剩餘時間: <span id="countdown" style='color:#F00;'></span><br />邀約結束時間: <span style='color:#36F;'><?=$odeadline;?></span></td></tr></table></div>
<div id="maindivbody">
  <table width="1020" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td colspan="3"><img src="images/main_9.gif" width="1020" height="13" /></td>
    </tr>
    <tr>
      <td width="17" valign="top" background="images/main_13.gif"><img src="images/main_10.gif" width="17" height="104" /></td>
      <td width="985" align="center"><div style="text-align:left;"><img src="images/title_ordermenu.gif" /></div><form action="orderlist.php" method="post" name="form1" id="form1"><div id='ordermenu' class='wrap'>
<?php

if(!empty($oderkey) && !empty($orderno))
{
	$k=1; //for cate columns
	$i=1; //for prod
	$maxcol=3;
	$cn="";
	
	if($pnos)
	{
		
		$strSql="SELECT pno,pname,pprice,pintro,ppic,psize,ptaste,ptemp,cname,psuit FROM ttprodtb AS P INNER JOIN ttcatetb C ON P.cno = C.cno WHERE pno IN ($pnos) ORDER BY C.cno";

		$data_rows=mysql_query($strSql,$link);
		$tt=mysql_num_rows($data_rows);
		
		while($rows = mysql_fetch_row($data_rows))
		{	
			$cname=$rows[8];
			
			//bottom
			if(($cn!=$cname && $cn!="")) 
			{
				echo "</table></td></tr></table><!--3 end--></td>
				  <td background='images/new_box02_right_bg.gif'></td>
				</tr>
				<tr>
				  <td><img src='images/new_box02_bottom_left.gif'  width='6' height='6' /></td>
				  <td background='images/new_box02_bottom_bg.gif'><img src='images/blank.gif'  width='1' height='1' /></td>
				  <td><img src='images/new_box02_bottom_right.gif'  width='6' height='6' /></td>
				</tr>
				</table><!--2 end--></div>";
				$k++;
			}
			
			//new line
			//if (($k%$maxcol)==1 && $cn!=$cname) echo "<tr>";
			
			//header
			if($cn!=$cname) 
			{
				echo "<div class='box'><!--2 start--><table cellspacing='0' cellpadding='0' align='center' border='0'>
					<tr>
					  <td width='6' height='6'><img src='images/new_box02_top_left.gif'  width='6' height='6' /></td>
					  <td background='images/new_box02_top_bg.gif'><img src='images/blank.gif'  width='1' height='1' /></td>
					  <td width='6' height='6'><img src='images/new_box02_top_right.gif'  width='6' height='6' /></td>
					</tr>
					<tr><td background='images/new_box02_left_bg.gif'></td>
					<td ><!--3 start--><table cellspacing='0' cellpadding='0' width='100%' border='0'>
					  <tr>
						<td height='37' align='left'><div style='background-image:url(images/h002img_02.jpg); height:37px; text-align:left;'><img src='images/h002img_01.jpg'  width='63' height='37' /><div style=' position:relative;top:-25px; left:63px; width:170px;font-size:14px; font-weight:bold; color:#6C06C0;'>$cname</div></div></td>
					  </tr>
					  <tr>
						<td><img src='images/new_gray_bar.gif'  width='100%' height='1' /></td>
					  </tr>
					  <tr align='left' valign='top'>
						<td height='85' style='PADDING-RIGHT:4px; PADDING-LEFT:4px; PADDING-TOP:3px;'><!--prodlist start--><table width='$tabwidth' border='0' cellspacing='1' cellpadding='1' bgcolor='#cccccc' class='prodlist'><tr><th>品名</th><th>售價</th><th>單位</th><th>冷熱</th><th>甜淡</th><th>數量</th></tr>";
					$cn=$cname;
			}
			
			prodlist($rows);
			
			//last record
			if($i==$tt)
			{
				echo "</table></td></tr></table><!--3 end--></td>
				  <td background='images/new_box02_right_bg.gif'></td>
				</tr>
				<tr>
				  <td><img src='images/new_box02_bottom_left.gif' width='6' height='6' /></td>
				  <td background='images/new_box02_bottom_bg.gif'><img src='images/blank.gif'  width='1' height='1' /></td>
				  <td><img src='images/new_box02_bottom_right.gif'  width='6' height='6' /></td>
				</tr>
				</table><!--2 end--></div>";
				$k++;
			}
			
			//if ((($k-1)%$maxcol)==0 && $cn!=$cname || $i==$tt) echo "</tr>";
			$i++;
		}
		
		if(count($pnoarr)>1)
			$pnostr = implode('-',$pnoarr); // add - among data
		else
			$pnostr = $pnoarr[0];
	}
	else
	{
		
	}
}
?>
        </div><div class="userarea" style="BORDER:#1c1c1c 1px solid;FONT-SIZE:12px;COLOR:#ff00ff;BACKGROUND-COLOR:#f3f3f3;padding-top:3px;padding-bottom:5px;"><label>訂購人姓名/暱稱:</label>
              <input style="FONT-SIZE: 11px;" name="username" type="text" id="username" size="20" value="<?=$_COOKIE['teatimeunick'];?>" />
              說明/電話:
              <input style="FONT-SIZE: 11px;" name="content" type="text" id="note" size="50" /><input name="lshow" type="checkbox" id="lshow" value="n" />悄悄訂<input type="hidden" name="pnostr" id="pnostr" value="<?=$pnostr;?>"><input type="hidden" name="otitle" id="otitle" value="<?=$otitle;?>"><input type="hidden" name="ono" id="ono" value="<?=$orderno;?>"><input type="hidden" name="ok" id="ok" value="<?=$oderkey;?>"><input type="hidden" name="odeadline" id="odeadline" value="<?=$odeadline;?>"><input type="hidden" name="ocreatetime" id="ocreatetime" value="<?=$ocreatetime;?>"><input type="hidden" name="operiod" id="operiod" value="<?=$operiod;?>"><input type="hidden" name="sno" id="sno" value="<?=$sno;?>">
              <input type="submit" name="savebutton" id="savebutton" value="送出訂單" />
            </div>
      </form></td>
      <td width="18" valign="top" background="images/main_14.gif"><img src="images/main_12.gif" width="18" height="104" /></td>
    </tr>
    <tr>
      <td colspan="3"><img src="images/main_15.gif" width="1020" height="16" /></td>
    </tr>
  </table>
</div>
</body>
</html>