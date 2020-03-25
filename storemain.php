<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";

function updatestore($sno)
{
	global $link; 
	$sintro=$_REQUEST["storeintro"];
	$sname =$_REQUEST["storename"];
	if(!empty($_REQUEST["thezip"])) $szip=$_REQUEST["thezip"];
	if($_REQUEST["thecity"]!="縣市") $scity=$_REQUEST["thecity"];
	if($_REQUEST["thecounty"]!="鄉鎮市區") $scounty=$_REQUEST["thecounty"];
	if(!empty($_REQUEST["storeaddr"])) $saddr=$_REQUEST["storeaddr"];
	
	if($_REQUEST["storeurl"]=="http://") $surl =""; else $surl=$_REQUEST["storeurl"];
	$stel =$_REQUEST["storetel"];
	$sfax =$_REQUEST["storefax"];
	$sdispatch =$_REQUEST["sdispatch"];
	$stype =$_REQUEST["storetype"];
	$lastupdate=date("Y-m-d H:i:s");
	
	$spicname=$_FILES["storepic"]["name"];
	$spicsize=$_FILES["storepic"]["size"];
	$spictype=$_FILES["storepic"]["type"];
	
	if(!empty($spicname))
	{
		if(($spicsize/1024)<1500 && ($spictype=="image/jpeg" || $spictype=="image/pjpeg" || $spictype=="image/gif"|| $spictype=="image/x-png"))
		{
			$FilePath="storepic/".$spicname;
			move_uploaded_file($_FILES["storepic"]["tmp_name"],$FilePath);
		}
		else
		{
			$spicname=$_REQUEST['stpich'];
			echo "<script>alert('上傳圖檔不成功，可能圖檔太大了，或是非jpg,gif,png圖檔格式!');</script>";
		}
	}
	else
		$spicname=$_REQUEST['stpich'];
	
	$strSql = "UPDATE ttstoretb SET sname='$sname',sintro='$sintro',szip='$szip',scity='$scity',scounty='$scounty',saddr='$saddr',surl='$surl',spic='$spicname',stel='$stel',sfax='$sfax',sdispatch='$sdispatch',stype='$stype',lastupdate='$lastupdate' WHERE sno=$sno";

	$ret=mysql_query($strSql,$link);
	
	if($ret==true) 
		return 1;
	else	
		return 0;
}

$savebtn=$_REQUEST['savebutton'];

if($savebtn=="確定送出")
{
	$snoarr=$_REQUEST['sno'];
	if(is_array($snoarr)) $sno=$snoarr[0]; else $sno=$snoarr;
	if(!empty($sno)) 
	if(updatestore($sno))
	{
		echo "<script>alert('更新成功');</script>";
	}
	else
		echo "<script>alert('更新失敗，可能伺服器忙碌或資料錯誤,請稍後再試');</script>";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TeaTime 茶飲訂購系統 修改店家</title>
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="css/validator.css">
<link rel="stylesheet" href="css/jquery.tooltip.css" />
<link rel="stylesheet" type="text/css" href="css/easyui.css">
<link rel="stylesheet" type="text/css" href="css/icon.css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="js/common.js" type="text/javascript"></script>
<script src="js/formValidator.js" type="text/javascript"></script>
<script src="js/formValidatorRegex.js" type="text/javascript"></script>
<script src="js/DateTimeMask.js" type="text/javascript"></script>
<script src="js/twzipcode-1.3.1.js" type="text/javascript"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script>
<script src="js/jquery.tooltip.js" type="text/javascript"></script>
<script src="js/jquery.easyui.min.js" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function(){
	//$.formValidator.initConfig({onError:function(){alert("校驗沒有通過，具體錯誤請看錯誤提示")}});
	$.formValidator.initConfig({onError:function(msg){alert(msg)}});
	$("#storename").formValidator({onshow:"請輸入店家名稱",onfocus:"店名可以中英混雜",oncorrect:"Thank you"}).InputValidator({min:1,max:100,onerror:"你輸入的長度必須介於1~100字之間,請確認"});
	
	$("#storetel").formValidator({onshow:"可以輸入市內電話或手機,也可以用逗號隔開",onfocus:"例如:0912-345678#1111或03-12345678",oncorrect:"Thank You"}).InputValidator({min:7,onerror:"店家電話位數不夠,請確認"});
	$("#storefax").formValidator({empty:true,onshow:"請輸入店家傳真",onfocus:"請輸入傳真號碼",oncorrect:"Thank You"}).InputValidator({min:7,onerror:"電話忘記填了或是碼數不對"});
	$("#storeaddr").formValidator({onshow:"請輸入店家地址",oncorrect:"Thank You"}).InputValidator({min:3,onerror:"店家地址不合法,請確認"});
	$("#storeurl").formValidator({onshow:"請輸入店家網址",oncorrect:"Thank You",defaultvalue:"http://"}).InputValidator({onerror:"網址不合法,請確認"});
	$("#storeintro").formValidator({onshow:"請輸入店家簡介或注意事項",onfocus:"如:<br>1.五十年老店,純手工釀製,適合老人入口即化<br>2.滿十杯才外送!!",oncorrect:"Thank You"}).InputValidator({min:3,onerror:"店家簡介請確認"});
	$("#storetype").formValidator({empty:true,onshow:"請輸入店家類型",onfocus:"如:中式,麵點,小吃,門市經營,便當",oncorrect:"Thank You"}).InputValidator({min:1,max:50,onerror:"店家類型請確認"});
	$("#storepic").formValidator({empty:true,onshow:"您可重新上傳店家圖片",onfocus:"限使用jpg,gif,png格式",oncorrect:"Thank You"}).InputValidator({min:1,onerror:"店家類型請確認"});
	$("#sdispatch").formValidator({onshow:"請輸入店家送達地區",onfocus:"如:桃園縣市,萬華區,全省,不含離島",oncorrect:"Thank You"}).InputValidator({min:0,max:100,onerror:"送達地區請確認"});
	$("#checknum").formValidator({onshow:"請輸入左圖中的驗證碼或點圖更新",onfocus:"驗證碼是英文字母",oncorrect:"驗證碼成功",onvalid:function(elem,val){
		if(val==$.cookie('teatimevcode')){
			return true;
		}else{
			return "驗證碼失敗,請重新輸入圖內文字"
		}}}).InputValidator({min:1,onerror:"驗證碼要輸入1個字,請確認"});

	$('#tabsdiv').tabs();
	
	$('#checknum').click(function(){$(this).val('');});
	
	
	$.post("searchstore.php",{srchtxt:""}, function(sdata){$('#thestores').html(sdata);});
	$('#searchbtn').click(function(){
		var searchtxt=$('#searchtxt').val();
		$.post("searchstore.php",{srchtxt:searchtxt}, function(sdata){$('#thestores').html(sdata);});
	});
	
	$('.tabs-inner[subid="tab3"]').click(function(){
		$('#stnamel').text($('#storename').val());
		$('#sttell').text($('#storetel').val());
		$('#stfaxl').text($('#storefax').val());
		$('#sturll').text($('#storeurl').val());
		$('#staddrl').text($('#storeaddr').val());
		$('#sttypel').text($('#storetype').val());
		$('#stdispatchl').text($('#sdispatch').val());
		$('#stpicl').text($('#storepic').val());
		$('#stpic2').attr("src",$('#stpicimg').attr("src"));
		$('#stintrol').text($('#storeintro').text());	
		
	});
	
});
 
</script>
<style>
#profiletb td{ border:solid 1px #999999;}
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
<div id="newsdiv"><img src="images/title_store.gif" width="432" height="44" alt="" /></div>
<div id="rightdiv">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="47" valign="top" background="images/sub01_19.gif"><img src="images/sub01_07-08.gif" width="47" height="28" alt="" /></td>
      <td valign="top" style="background-image:url(images/sub01_09.gif); background-position:top;">&nbsp;</td>
      <td width="24" height="28" valign="top" background="images/sub01_14.gif"><img src="images/sub01_10.gif" width="24" height="28" alt="" /></td>
    </tr>
    <tr>
      <td valign="top" background="images/sub01_19.gif"><img src="images/sub01_09-13.gif" width="47" height="121" alt="" /></td>
      <td align="center" valign="top"><form method="post" enctype="multipart/form-data" name="form1" id="form1" onSubmit="return jQuery.formValidator.PageIsValid('1')"><div id="tabsdiv" style="width:680px;height:auto; padding-left:10px;">
			<div title="第一步: 選擇要修改的店家" style="padding:20px;"><div style="text-align:center; padding-bottom:10px; "><input type="text" id="searchtxt" name="searchtxt" style="width:200px" /> <input type="button" id="searchbtn" name="searchbtn" style="width:60px" value="搜尋店家" /><input type="hidden" id="poty" value="st" /><br />(您可以輸入任意的關鍵字,如:店名、店家類型、送達地區、電話、地址、店家簡介等)</div><div id="thestores">
</div></div>
			<div title="第二步: 根據提示修改店家資料" style="padding:20px;" id="storeform">
  <table border="0px" style="font-size:12px;" width="630px">
  <tr> 
      <td align="right">店名:</td>
      <td align="left"><input type="text" id="storename" name="storename" style="width:180px" /></td>
      <td><div id="storenameTip" style="width:250px;"></div></td>
    </tr>
    <tr> 
      <td align="right">電話:</td>
      <td align="left"><input type="text" id="storetel" name="storetel" style="width:120px" /></td>
      <td><div id="storetelTip" style="width:250px;"></div></td>
      </tr>
    <tr> 
      <td align="right">傳真:</td>
      <td align="left"><input type="text" id="storefax" name="storefax" style="width:120px" /></td>
      <td><div id="storefaxTip" style="width:250px;"></div></td>
      </tr>
      <tr> 
      <td align="right">網址:</td>
      <td align="left"><input type="text" id="storeurl" name="storeurl" style="width:250px" /></td>
      <td><div id="storeurlTip" style="width:250px;"></div></td>
      </tr>
        <tr> 
      <td align="right" valign="top">地址:</td>
      <td align="left"><div id="addrcontainer"></div><input class="theaddr" type="text" id="storeaddr" name="storeaddr" style="width:250px" />
      </td>
      <td><div id="storeaddrTip" style="width:250px;"></div></td>
        </tr>
    <tr> 
      <td align="right">類型:</td>
      <td align="left"><input type="text" id="storetype" name="storetype" style="width:250px" /></td>
      <td><div id="storetypeTip" style="width:250px;"></div></td>
      </tr>

    <tr> 
      <td align="right">送達地區:</td>
      <td align="left"><input type="text" id="sdispatch" name="sdispatch" style="width:250px" /></td>
      <td><div id="sdispatchTip" style="width:250px;"></div></td>
      </tr>

    <tr> 
      <td align="right" valign="top">上傳圖片:</td>
      <td align="left" valign="top"><input type="file" id="storepic" name="storepic" style="width:200px" />
        <img class="tonus2" id="stpicimg" alt="店家圖片" src="storepic/nostore.jpg" width="35" height="30" /><input type="hidden" id="stpich" name="stpich"  /></td>
      <td><div id="storepicTip" style="width:250px;"></div></td>
      </tr>
        <tr> 
      <td align="right">簡介與規則:</td>
      <td align="left" valign="top"><textarea name="storeintro" rows="6" id="storeintro" style="width:250px" type="text"></textarea></td><td><div id="storeintroTip" style="width:250px; height:50px; text-align:left;"></div></td>
      </tr>
  </table>
  </div>
	<div title="第三步: 預覽修改結果並確定送出" style="padding:20px;" id="previewstdata">
    <input name="checknum" type="text" id="checknum" style="width:60px; height:30px; font-size:20px; font-weight:bold;" />
            <img src="verify_image.php" alt="點此刷新驗證碼" name="verify_code" width="120" height="50" border="0" id="verify_code"
onclick="this.src='verify_image.php?'+ Math.random();" style="cursor:pointer;" /><div id="checknumTip" style="width:250px;"></div>
	  <table width="450" border="0" cellpadding="4" cellspacing="1" id="profiletb">
	    <tr>
	      <td width="100" align="right">店名:</td>
	      <td align="left"><label id="stnamel"></label></td>
	      </tr>
	      <td align="right">聯絡電話:</td>
	      <td align="left"><label id="sttell"></label></td>
	      </tr>
	    <tr>
	      <td align="right">傳真:</td>
	      <td align="left"><label id="stfaxl"></label></td>
	      </tr>
	    <tr>
	      <td align="right">店家網址:</td>
	      <td align="left"><label id="sturll"></label></td>
	      </tr>
	    <tr>
	      <td align="right" valign="top">聯絡地址:</td>
	      <td align="left"><label id="staddrl"></label></td>
	      </tr>
	    <tr>
	      <td align="right">類型:</td>
	      <td align="left"><label id="sttypel"></label></td>
	      </tr>
	    <tr>
	      <td align="right">送達地區:</td>
	      <td align="left"><label id="stdispatchl"></label></td>
	      </tr>
	    <tr>
	      <td align="right" valign="top">店家圖片:</td>
	      <td align="left" valign="top"><img class="tonus2" id="stpic2" width="35" height="30" /><label id="stpicl"></label></td>
	      </tr>
	    <tr>
	      <td align="right">簡介與規則:</td>
	      <td align="left"><label id="stintrol"></label></td>
	      </tr>
	    </table><input type="submit" name="savebutton" id="savebutton" value="確定送出" style="height:30pt; width:50pt;" />

					</div>
					 
		</div></form>
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
