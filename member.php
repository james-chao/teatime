<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TeaTime 茶飲訂購系統 個人資料</title>
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/easyui.css">
<link rel="stylesheet" type="text/css" href="css/icon.css">
<link rel="stylesheet" href="css/jquery.tooltip.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="js/common.js" type="text/javascript"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script>
<script src="js/jquery.tooltip.js" type="text/javascript"></script>
<script src="js/jquery.easyui.min.js" type="text/javascript"></script>
<script src="js/easyui-lang-zh_TW.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
	$('img[id$=button]').css({ opacity: 0.7}).click(function(){$('#srchdiv').hide();}).mouseover(function(){$(this).css({ opacity: 1,border:'dotted 2px #666' })}).mouseout(function(){$(this).css({ opacity: 0.7,border:'0px' })});
	$('#edtbutton').click(function(){$.post("editprofile2.php",function(data){$('#fundiv').html(data);});});
	$('#listbutton').click(function(){$.post("userprofile2.php",function(data){$('#fundiv').html(data);});});
	$('#historybutton').click(function(){$.post("myhistory2.php",function(data){$('#fundiv').html(data);});});
	$('#uploadbutton').click(function(){$.post("uploaddata2.php",function(data){$('#fundiv').html(data);});});
	$('#ordermainbutton').click(function(){$.post("myorder2.php",function(data){$('#fundiv').html(data);});});
	$('#fdinvitebutton').click(function(){location.href='friendinvite.php';});
	//$('#addfdreqbutton').click(function(){location.href='tobefriend.php';});
	
	$('#storecopybutton').click(function(){
		$('#poty').val('sc');
		$('#dwsctytext').text('分店複製');
		$.post("searchstore.php",{srchtxt:""}, function(sdata){$('#srchdiv').show();$('#fundiv').html(sdata);});	
	});
	$('#downprodbutton').click(function(){
		$('#poty').val('dw');
		$('#dwsctytext').text('商品下載');
		$.post("searchstore.php",{srchtxt:""}, function(sdata){$('#srchdiv').show();$('#fundiv').html(sdata);});	
	});
	$('#searchbtn').click(function(){
		var searchtxt=$('#searchtxt').val();
		$.post("searchstore.php",{srchtxt:searchtxt}, function(sdata){$('#srchdiv').show();$('#fundiv').html(sdata);});
	});

});	
</script>
<style>
#userprofiletb td{ border:solid 1px #999999;}
#btnlist img{cursor:pointer;}
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
<div id="newsdiv"><img src="images/title_member.gif" width="432" height="44" alt="" /></div>
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
	  <div style="background-image:url(images/h002img_02.jpg);width:620px; height:40px; text-align:left; margin-left:30px;"><img src="images/h002img_01.jpg" alt="" width="63" height="37" />
	  <div style=" position:relative;top:-30px; left:80px;font-size:16px; font-weight:bold; ">請選擇您要服務的選項</div></div>
	  <div id="btnlist" style="padding:2px;">
	  <img id="listbutton" src="images/btn_userprofile.jpg" alt="檢視您在本站的個人資料" />
	  <img id="edtbutton" src="images/btn_editprofile.jpg" alt="編輯您在本站的個人資料" />
	  <img id="uploadbutton" src="images/btn_uploaddata.jpg" alt="在excel編輯好的檔案，可以快速上傳到本系統中" />
	  <img id="historybutton" src="images/btn_orderhistory.jpg" alt="您個人在本站過去所曾訂購過的各項商品記錄" /><br />
	  <img id="storecopybutton" src="images/btn_storecopy.jpg" alt="您可以使用已存有的店家資料，另外備份到系統，以便快速維護另一個分店的商品" />
	  <img id="downprodbutton" src="images/btn_downprod.jpg" alt="選擇店家後下載，把商品修改一下，就能再上傳成自己選的新店家囉" />
	  <img id="ordermainbutton" src="images/btn_ordermain.jpg" alt="選擇您的訂單後，重新編訂單中的內容" />
	  <!--img src="images/btn_addfdrequest.jpg" alt="可以查看有那些人希望加入成為您的朋友，您也可以向其它人提出加入請求" id="addfdreqbutton" /><br /-->
	  <!--img id="fdinvitebutton" src="images/btn_friendinvite.jpg" alt="看朋友發起了那些的訂購邀約" /--></div>
	  <div style=" border-top:dashed #999; width:95%"></div>
	  <div style="display:none;text-align:center; padding-bottom:10px; " id='srchdiv'><span id="dwsctytext" style="color:#ff0000;"></span>:<input type="text" id="searchtxt" name="searchtxt" style="width:200px" /> <input type="button" id="searchbtn" name="searchbtn" style="width:60px" value="搜尋店家" /><input type="hidden" id="poty" /><br />(您可以輸入任意的關鍵字,如:店名、店家類型、送達地區、電話、地址、店家簡介等)</div><div id="fundiv" style="padding:5px;"></div></td>
      <td background="images/sub01_14.gif">&nbsp;</td>
    </tr>
    <tr>
      <td width="47" height="21" valign="bottom" background="images/sub01_19.gif"><img src="images/sub01_22.gif" width="47" height="21" alt="" /></td>
      <td valign="bottom" background="images/sub01_24.gif">&nbsp;</td>
      <td width="24" height="21" valign="bottom" background="images/sub01_14.gif"><img src="images/sub01_25.gif" width="24" height="21" alt="" /></td>
    </tr>
  </table><div id="sc" class="easyui-window" closed="true" modal="true" title="分店複製" style="text-align:center;display:none; background-color:#eee;border:solid 1px #999; width:308px; height:160px; font-size:12px;">
		<p style='text-align:left; padding:5px;'>您確定要複製這個分店的產品嗎?<br>請填入新分店的名稱後按<span style='color:#0000FF'>複製</span>，否則請按<span style='color:#ff0000'>取消</span>!!</p>
        分店名稱:<input type="text" style='width:220px;color:#0000ff' id="newstorename">
        <p><a class="easyui-linkbutton" iconCls="icon-ok" href="javascript:void(0)">複製</a>&nbsp;&nbsp;&nbsp;<a class="easyui-linkbutton" iconCls="icon-cancel" href="javascript:void(0)">取消</a></p></div>
</div>
</body>
</html>
