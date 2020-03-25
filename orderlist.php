<?php
header( 'Content-Type: text/html; charset=utf-8' );
include_once(dirname(__FILE__) . '/Classes/utility.php');
ini_set('memory_limit', '-1'); //unlimited memory usage of server

if($_REQUEST['ono'])
{
	$oderkey=$_REQUEST['ok'];
	$orderno=$_REQUEST['ono'];
	$epxtime=365*24*60*60;
	setcookie("teatimeorderkey",$oderkey,time()+$epxtime);
	setcookie("teatimeorderno",$orderno,time()+$epxtime);
}
else
{
	$oderkey=$_COOKIE['teatimeorderkey'];
	$orderno=$_COOKIE['teatimeorderno'];
	//$oderkey="5d6351161ae7f20f04c5a5b665ea27b3";
	//$orderno=8;
}
if ($oderkey=="" || $orderno==""){
	if ($_COOKIE['teatimeuuser']!="" || $_COOKIE['teatimeutype']!="")
		echo "<script language='javascript'>alert('發起人請登入後從我的邀約選擇訂單,或在首頁輸入訂單碼');history.back();</script>";
	else
		echo "<script language='javascript'>alert('訂購人請確定您已取得正確的訂單碼,並已開啟Cookie！');location.href='/';</script>";	
}

require_once "conn.php";

$ttordered="teatimeordered".$orderno; //for two new orders in paralle
//for period order
if($oderkey!="" && $orderno!="")
{
	$strSQL="SELECT operiod,ocreatetime,odeadline FROM ttordertb WHERE ono=$orderno";
	$data=mysql_query($strSQL);
	$rows=mysql_fetch_array($data);
	$oprd=$rows[0];
	$cttime=$rows[1];
	$ddline=$rows[2];
	$oprdarr=explode("#",$oprd);
	$pdtype=$oprdarr[0];
	$pdvalue=$oprdarr[1]; 
	$now=date("Y-m-d H:i:s");
	
	$selcycno=$_REQUEST['selcycno'];
	if($selcycno=="") $selcycno=0;
	switch($pdtype)
	{
		case "none":
			$ttcycno=0;
			$ctcycno=0;
			break;
		case "days": 
			$sndiff=dateDiff($cttime,$now);
			$sediff=dateDiff($cttime,$ddline);
			$ttcycno=$sediff/$pdvalue; //all cycles: start to deadline cycles
			$ctcycno=$sndiff/$pdvalue; //current cycle
			break;
		case "weeks":
			$ttcycno=weekcycno($cttime,$ddline,$pdvalue); //all cycles
			$ctcycno=weekcycno($cttime,$now,$pdvalue); //current cycle
			break;
	}
	
	if($pdtype!="" && $pdtype!="none")
	{
		$selcycstr ="周期數:<select id='selcycno' name='selcycno'>";
		for($y=($ttcycno-1);$y>=0;$y--)
		{
			if($ctcycno<$y) continue; //hide the future cycles
			if($selcycno=="") 
			{
				$selcycno=$ctcycno;
				$selcycstr .=($ctcycno!=$y)?"<option value='$y'>周期".($y+1)."</option>":"<option value='$y' selected='selected'>周期".($y+1)."</option>";
			}
			else
				$selcycstr .=($selcycno!=$y)?"<option value='$y'>周期".($y+1)."</option>":"<option value='$y' selected='selected'>周期".($y+1)."</option>";
		}
		$selcycstr .="</select>";
	}

}
//form ordermenu.php submit 
if($_REQUEST['savebutton']=="送出訂單" && $orderno!="" && !isset($_SESSION[$ttordered]))
{
	//double check whether an order is past
	$ordertime=date("Y-m-d H:i:s");
	$odeadline=$_REQUEST['odeadline'];
	$lshow=($_REQUEST['lshow']=="n")?'n':'y'; //hide if chekced (value='n')

	if($odeadline!="")
	{
		if(strtotime($odeadline)-strtotime($ordertime)<0) 
			echo "<script>alert('本單已過期，不得再送!!');history.go(-1);</script>";
		else
		{ //for period order
			$operiod=$_REQUEST['operiod'];
			$ocreatetime=$_REQUEST['ocreatetime'];
			
			$periodarr=explode("#",$operiod);
			$pdtyp=$periodarr[0];
			$pdval=$periodarr[1]; 
			$err=0;
			switch($pdtyp)
			{
				case "none":
					$cycno=0;
					break;
				case "days": 
					$sndiff=dateDiff($ocreatetime,$ordertime); //create time to now
					$endiff=dateDiff($ordertime,$odeadline);
					if($endiff>=0 && $pdval>0 && ($sndiff%$pdval)==0) 
						$cycno=$sndiff/$pdval;
					else
						$err++;
					break;
				case "weeks":
					$endiff=dateDiff($ordertime,$odeadline); //now to deadline
					if($endiff>=0 && in_array(date("w",strtotime($ordertime)),explode("-",$pdval)))
						$cycno=weekcycno($ocreatetime,$ordertime,$pdval);
					else
						$err++;
					break;
			} //switch
			
		}
		if($err) echo "<script>alert('訂單處理發生錯誤');history.go(-1);</script>";
	}
	else
		echo "<script>alert('本單可能忘了設結束時間，煩請邀約發起人修改設定');history.go(-1);</script>";

	$k=0;
	if (isset($_POST['pqty']) && is_array($_POST['pqty']) && count($_POST['pqty']) > 0) 
	{ 
		$pqtyarr=$_REQUEST['pqty'];
		$psize=$_REQUEST['psize'];
		$pprice=$_REQUEST['pprice'];
		$ptemp=$_REQUEST['ptemp'];
		$ptaste=$_REQUEST['ptaste'];
		$pnostr=$_REQUEST['pnostr'];
		
		if(!empty($pnostr)) $pnoarr=explode("-",$pnostr);
		
		$nickname=trim($_REQUEST['username']);
		$content=str_replace(",",".",trim($_REQUEST['content']));
		
		
		
		$strSql="";
		foreach ($pqtyarr as $pqty) 
		{ 
			if($pqty!=""&& $pqty>0)
			{
				//no allow same user in a new order
				//$sql="SELECT username FROM orderlist WHERE nno=$nno AND username='$user'";
				//$list2=mysql_query($sql,$link);
				//if(!list($username)=mysql_fetch_row($list2))
				//{
				$uflag=$_COOKIE['teatimeuflag'];
				$poption=$psize[$k]."-".$ptemp[$k]."-".$ptaste[$k];
				$strSql .="INSERT INTO ttlisttb (ono,pno,cycno,uoption,pprice,pqty,unick,uflag,content,lshow,ordertime) VALUES ($orderno,$pnoarr[$k],$cycno,'$poption',$pprice[$k],$pqty,'$nickname','$uflag','$content','$lshow','$ordertime');";
				
				//}
			}
		   $k++;     
		} 
/*echo $strSql;
exit;*/
		if($strSql) 
		{
			if(isset($_SESSION[$ttordered]) && $_SESSION[$ttordered]=="done")
				$ret=false;	//for user page reload 
			else 
				$ret=mysqli_multi_query($link2,$strSql);  //save user's order
			
			if($ret==true)
			{
				$epxtime=365*24*60*60;
				setcookie("teatimeunick",$nickname,time()+$epxtime); //for new keyin nickname
				//setcookie("teatimeoldunick",'',time()+$epxtime); //clean up old-nickname
				if(!isset($_COOKIE['teatimeoldunick']) ||($nickname!="" && isset($_COOKIE['teatimeoldunick']) && strpos($_COOKIE['teatimeoldunick'],"#$nickname#")===false))
						setcookie("teatimeoldunick","#$nickname#".$_COOKIE['teatimeoldunick'],time()+$epxtime); //for all past nicknames
				if(!isset($_COOKIE['teatimeoldono']) ||($orderno!="" && isset($_COOKIE['teatimeoldono']) && strpos($_COOKIE['teatimeoldono'],"#$orderno#")===false))
					setcookie("teatimeoldono","#$orderno#".$_COOKIE['teatimeoldono'],time()+$epxtime); //won't rewrite same order again
				$_SESSION[$ttordered]="done"; // for F5 or page refresh
			}
			
		}
		else
			echo "<script>alert('請確認已輸入商品數量');history.back();</script>";
	}	
	else
		echo "<script>alert('請確認已輸入商品數量');history.back();</script>";	
}

$act=$_REQUEST['act'];
$lno=$_REQUEST['lno'];
if($act="del" && intval($lno)>0)
{
	$orderno=$_COOKIE['teatimeorderno'];
	$usernick=$_COOKIE['teatimeunick'];
	if($orderno && $usernick)
	{
		$uf=$_REQUEST['uf'];
		$hr=$_REQUEST['hr']; //holder
		$ckuflag=$_COOKIE['teatimeuflag'];
		if($uf==$ckuflag)
			$strSql="DELETE FROM ttlisttb WHERE lno=$lno AND uflag='$uf'";
		else if($hr=="y")
			$strSql="DELETE FROM ttlisttb WHERE lno=$lno";
			
		$ret=mysql_query($strSql,$link);
		if($ret==true) unset ($_SESSION[$ttordered]); //re-order again
	}
}

function dateDiff($startTime, $endTime) {
    $start = strtotime(date("Y-m-d",strtotime($startTime))); //just Y-m-d, no h:i:s
    $end = strtotime(date("Y-m-d",strtotime($endTime)));
    $timeDiff = $end - $start;
    return floor($timeDiff / (60 * 60 * 24));
}
function weekcycno($startTime, $endTime,$weekChk) {
	$start = date("Y-m-d",strtotime($startTime));
	$days = dateDiff($startTime, $endTime);
	$cyc=0; //the order created day
	
	for($x=1;$x<=$days;$x++) //from second day
	{
		$varday = strtotime("$start +$x day");
		$matched = in_array(date("w",$varday),explode("-",$weekChk));
		if($matched) $cyc++;
	}
	return $cyc;
   
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TeaTime 茶飲訂購系統 檢視訂單</title>
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/easyui.css">
<link rel="stylesheet" type="text/css" href="css/icon.css">
<link rel="stylesheet" href="css/jquery.tooltip.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="js/common.js" type="text/javascript"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script>
<script src="js/jquery.tooltip.js" type="text/javascript"></script>
<script type="text/javascript" src="js/jquery.ui.js"></script>
<script src="js/jquery.editinplace.js" type="text/javascript"></script>
<script src="js/jquery.easyui.min.js" type="text/javascript"></script>
<!--<script type="text/javascript" src="js/jquery.colorize-2.0.0.js"></script>-->
<script type="text/javascript">
$(document).ready(function(){
	$('#tabsdiv').tabs();
	$('.tonus').tooltip({delay: 0, showURL: false, bodyHandler: function() {return $("<img/>").attr("src", this.src);}});
	$('.tips').tooltip();
	//$(".profiletb").colorize();

	$(".edittext").editInPlace({
		url: "passnewdata.php",
		//text_size:1,
		saving_text:"儲存中..."
	});
	$('.rmbtn').click(function(){
		var lno=$(this).attr('id');
		var uf=$(this).attr('uf');
		var hr=$(this).attr('hr');
		$.messager.confirm('再次確認', '您確定要刪除這個訂購商品嗎?', function(r){
			if(r) 
			{
				location.href='?act=del&lno='+lno+'&uf='+uf+'&hr='+hr;
				return false;
			}
		});
	});
	$("#selcycno").change(function(){
 		location.href="?selcycno="+$(this).val();
	});
	$('.profiletb tr:even').css('background-color','#FFFFDC');
	$('.profiletb tr:odd').css('background-color','#FFFFFF');
});
 
</script>
<style>
.profiletb{ background-color:#cccccc}
.profiletb td{ border:solid 0px #999999;}
select{font-size:10px;width:60px;height:20px;}
</style>
</head>
<body>
<div id="topdiv"><div style="width:385px;"><img src="images/olist1.jpg" width="385" height="220" alt=""></div><div style="width:385px;  position:relative; left:385px; top:-220px;"><img src="images/olist2.jpg" width="385" height="220" alt=""></div></div>
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
      <td align="center" valign="top">
<?
$otitle=$_REQUEST['otitle'];

if(empty($otitle)) 
{
	$strSql="SELECT otitle,onote,ocreatetime,odeadline FROM ttordertb WHERE ono=$orderno AND oorderkey='$oderkey'";
	$data_rows=mysql_query($strSql,$link);
	if($rows = mysql_fetch_row($data_rows))
	{
		$otitle=$rows[0];
		$tips="<font style=\"font-size:12px;color:#ff0000;\">起始時間: $rows[2] <br> 終止時間: $rows[3]</font>";
	}
}

echo "<div style='font-weight:bold;font-size:16px;color:#678907' class='tips' title='$tips'><span style='color:#000000;'>邀約主題:</span> $otitle</div>";

	//select user order list
	$strSql="SELECT lno,pname,L.pprice,uoption,pqty,L.unick,content,ordertime,O.uemail,odeadline,L.uflag,lshow FROM ttlisttb AS L INNER JOIN ttprodtb AS P INNER JOIN ttordertb AS O ON L.pno=P.pno AND O.ono=L.ono WHERE O.ono=$orderno AND L.cycno=$selcycno ORDER BY ordertime desc,L.unick";

	$data_rows=mysql_query($strSql);
	
?><div style="text-align:right; padding-right:40px;"><?=$selcycstr;?>&nbsp;&nbsp;&nbsp;&nbsp;<a href="downorderlist.php?ono=<?=$orderno;?>&cycno=<?=$selcycno;?>">下載本訂單:<img src="images/excelicon.gif" width=18 height=18 /></a></div><div id="tabsdiv" style="width:680px;height:auto; padding-left:10px;">
       <div title="以明細方式檢視訂單" style="text-align:center;padding:10px;"><div style="text-align:left;padding:10px; font-size:11px;color:#999999;width:300px; float:left;"><ul style="list-style:inside"><li style="list-style:square;">您可以在線上直接修改或刪除您的訂購資料。</li><li style="list-style:square;">只有邀約發起人才有修改此訂單的所有權限。</li>
       <li style="list-style:square;">發起人的註冊信箱與寄送信箱不同時，則無法修改。</li><li style="list-style:square; color:#ff0000">修改後，請記得重新整理。</li></ul></div><div style="text-align:left; padding:10px; font-size:11px;color:#999999;width:300px;float:left;">
<?
$sno=0;
$strSQL2="SELECT sno FROM ttordertb WHERE ono=$orderno";
$rows=mysql_query($strSQL2);
list($sno)=mysql_fetch_row($rows);

$strSQL2="SELECT sname,stel,sfax,saddr,spic,surl FROM ttstoretb WHERE sno=$sno";
$data_rows2=mysql_query($strSQL2);
if(list($sname,$stel,$sfax,$saddr,$spic,$surl)=mysql_fetch_row($data_rows2))
{
	if($surl)
		$storedata="<a href='$surl' target='_blank'><img alt='Fax:$sfax\n$sintro' src='storepic/$spic' width='70' height='45' align='left' style='margin-right:6px;' /></a>店名:$sname<br />電話:$stel<br />$saddr";
	else
		$storedata="<img alt='Fax:$sfax\n$sintro' src='storepic/$spic' width='70' height='45' align='left' style='margin-right:6px;' />店名:$sname<br />電話:$stel<br />$saddr";
	echo $storedata;
}
?></div>
<?
	  
	$i=0;
	echo "<table width='98%' border='0' cellspacing='1' cellpadding='0' class='profiletb'><tr><th>編號</th><th>品名</th><th>單價</th><th>訂購人</th><th>選項</th><th>說明</th><th>數量</th><th>小計</th><th>訂購時間</th><th>刪除</th></tr>";
	while(list($lno,$pname,$pprice,$uoption,$pqty,$unick,$content,$ordertime,$holder,$odeadline,$uflag,$lshow)=mysql_fetch_row($data_rows))
	{
		$i++;
		$sum=$pqty*$pprice;
		//$totalcost +=$sum;
		$un=($nickname!="")?$nickname:($_COOKIE['teatimeunick']!="")?$_COOKIE['teatimeunick']:'';
		$uuser=$_COOKIE['teatimeuuser'];
		$diff=strtotime($odeadline)-strtotime(date("Y/m/d H:i:s"));
		$ckuflag=$_COOKIE['teatimeuflag'];
		
		//hide the order if no-show checked
		if($lshow=="n" && $uuser!=$holder && !($uflag==$ckuflag && ($un==$unick || strpos($_COOKIE['teatimeoldunick'],"#$unick#"))))
		{
			echo "<tr><td>$i</td><td>--</td><td>--</td><td>$unick</td><td>--</td><td>--</td><td>$pqty</td><td>--</td><td>$ordertime</td><td>&nbsp;</td></tr>";
			continue; 
		}
		if($content=="") $content="(沒有留言)";
		if($diff>0 && ($uuser==$holder || ($uflag==$ckuflag && ($unick==$nickname || strpos($_COOKIE['teatimeoldunick'],"#$unick#") || $un==$unick)))) 
		{
			$pqty="<a href='javascript:void(0);'>$pqty</a>";
			if($content!="") $content="<a href='javascript:void(0);'>$content</a>"; else $content="&nbsp;";
			$userstring="<td><p class='edittext' id='p-$lno'><a href='javascript:void(0);'>$uoption</a></p></td><td align='left'><p class='edittext' id='c-$lno'>$content</p></td><td><p class='edittext' id='q-$lno'>$pqty</p></td>";
			$delimg="<img src='css/icons/no.png' class='rmbtn' id='$lno' hr='y' uf='$uflag' />";
		}
		else
		{
			
			$userstring="<td>$uoption</td><td align='left'><p>$content</p></td><td><p>$pqty</p></td>";
			$delimg="&nbsp;";
		}
		echo "<tr><td>$i</td><td>$pname</td><td>$pprice</td><td>$unick</td>$userstring<td>$sum 元</td><td>$ordertime</td><td>$delimg</td></tr>";
		
	}
	echo "</table>";
		?></div>
        <div title="以商品名檢視訂單" style="text-align:center;padding:10px;"><?
	
	$totalcost=0;
	$strSql="SELECT pname,L.pprice,ppic,sum(pqty),GROUP_CONCAT(CONCAT('&diams;',unick,':',uoption,'/ ',content,'/ 數量:',pqty)) FROM ttlisttb AS L INNER JOIN ttprodtb AS P ON L.pno=P.pno WHERE ono=$orderno AND L.cycno=$selcycno GROUP BY pname,L.pprice,ppic ORDER BY L.pno";
	$data_rows=mysql_query($strSql,$link);
	$i=0;
	echo "<table width='98%' border='0' cellspacing='1' cellpadding='0' class='profiletb'><tr><th>編號</th><th>品名</th><th>圖片</th><th>單價</th><th>總數</th><th>說明</th><th>合計</th></tr>";
	while(list($pname,$pprice,$ppic,$pqty,$remark)=mysql_fetch_row($data_rows))
	{
		$i++;
		$totalcost +=$pqty*$pprice;
		$totalqty +=$pqty; //rapid sum up
		if($ppic) $prodimg="<img src='prodpic/$ppic' width='10' height='10' class='tonus' />"; else $prodimg="&nbsp;";
		echo "<tr><td>$i</td><td>$pname</td><td>$prodimg</td><td>$pprice</td><td>$pqty</td><td align='left'>".str_replace(",","<br />",$remark)."</td><td>".($pqty*$pprice)."元</td></tr>";
		
	}
	echo "</table>";
		?></div>
        <div title="以訂購人檢視訂單" style="text-align:center;padding:10px;"><?
	//$totalcost=0;

	$strSql="SELECT unick,GROUP_CONCAT(CONCAT('&spades;',pname,':',L.pprice,'元/ '),REPLACE(uoption,',','/ '),CONCAT('/ 數量:',pqty)),content,sum(pqty*L.pprice),lshow FROM ttlisttb AS L INNER JOIN ttprodtb AS P ON L.pno=P.pno WHERE ono=$orderno   AND L.cycno=$selcycno GROUP BY unick ORDER BY unick,ordertime desc";
	$data_rows=mysql_query($strSql,$link);
	$i=0;
	echo "<table width='98%' border='0' cellspacing='1' cellpadding='0' class='profiletb'><tr><th>編號</th><th>訂購人</th><th>品項 & 內容</th><th>說明</th><th>小計</th></tr>";
	while(list($unick,$pdetail,$content,$sumprice,$lshow)=mysql_fetch_row($data_rows))
	{
		$i++;
		//$totalcost +=$sumprice;
		if($lshow=="n" && $uuser!=$holder && !($uflag==$ckuflag && ($un==$unick || strpos($_COOKIE['teatimeoldunick'],"#$unick#"))))
		{
			echo "<tr><td>$i</td><td>$unick</td><td>--</td><td>--</td><td>--</td></tr>";
			continue; 
		}
		
		echo "<tr><td>$i</td><td>$unick</td><td align='left'>".str_replace(",","<br />",$pdetail)."</td><td>$content</td><td>$sumprice 元</td></tr>";
		
	}
	echo "</table>";
		?></div>
        </div><hr size='1' /><div><span style="color:#F00;">總計</span> : <?=$totalcost;?> 元 / <span style="color:#00F;">總數</span> : <?=$totalqty;?> (單位)</div>
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