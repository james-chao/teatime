<?
if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TeaTime 茶飲訂購系統 最愛店家</title>
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link href="css/cloudcarousel.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/easyui.css">
<link rel="stylesheet" type="text/css" href="css/icon.css">
<link rel="stylesheet" href="css/jquery.tooltip.css" />
<link type="text/css" href="css/jquery.rater.css" rel="stylesheet"/>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script>
<script type="text/JavaScript" src="js/jquery.mousewheel.js"></script>
<script type="text/JavaScript" src="js/cloud-carousel.1.0.5.js"></script>
<script src="js/twzipcode-1.3.1.js" type="text/javascript"></script>
<script src="js/common.js" type="text/javascript"></script>
<script src="js/jquery.easyui.min.js" type="text/javascript"></script>
<script src="js/easyui-lang-zh_TW.js" type="text/javascript"></script>
<script src="js/jquery.rater.js" type="text/javascript"></script>
<script src="js/jquery.tooltip.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
	$.post("loadcarousel.php", function(data){
		if(data=="") data="<div style='color:#ff0000;text-align:center;'>您尚未新增任何店家!!</div>";
		$('#loadcarousel').html(data);
	});
	$('#addrcontainer').twzipcode();
	$('.thezip').hide();
	$('#tabsdiv').tabs();
	$.post("searchstore.php",{srchtxt:'',city:'',county:''}, function(sdata){$('#thestores').html(sdata);});
	$('#searchbtn').click(function(){
		var thecity=$('.thecity option:selected').val();
		var thecounty=$('.thecounty option:selected').val();
		var searchtxt=$('#searchtxt').val();
		$.post("searchstore.php",{srchtxt:searchtxt,city:thecity,county:thecounty}, function(sdata){$('#thestores').html(sdata);});
	});
	$('.showmystore').click(function(){$('#thestores').slideToggle();});

});
</script>

<style>
#profiletb td{ border:solid 1px #999999;}
</style>
</head>
<BODY>
<div id="topdiv"><div style="width:385px;"><img src="images/version_03.jpg" width="385" height="220" alt=""></div><div style="width:385px;  position:relative; left:385px; top:-220px;"><img src="images/version_04.jpg" width="385" height="220" alt=""></div></div>
<div id="leftdiv"><div style="width:50px; height:149px; "><img src="images/sub01_01.jpg" width="250" height="149" id="getT" /></div>
<div id="newsdiv"><img src="images/title_version.gif" width="432" height="44" alt="" /></div>
<div id="rightdiv">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="47" valign="top" background="images/sub01_19.gif"><img src="images/sub01_07-08.gif" width="47" height="28" alt="" /></td>
      <td valign="top" style="background-image:url(images/sub01_09.gif); background-position:top;">&nbsp;</td>
      <td width="24" height="28" valign="top" background="images/sub01_14.gif"><img src="images/sub01_10.gif" width="24" height="28" alt="" /></td>
    </tr>
    <tr>
      <td valign="top" background="images/sub01_19.gif"><img src="images/sub01_09-13.gif" width="47" height="121" alt="" /></td>
      <td valign="top"><form action="savemystore.php" enctype="multipart/form-data" method="post">
        <div style="background-image:url(images/h002img_02.jpg);width:620px; height:40px; text-align:left; margin-left:30px;"><img src="images/h002img_01.jpg" alt="" width="63" height="37" />
          <div style=" position:relative;top:-35px; left:80px;font-size:16px; font-weight:bold; ">找尋要加入的店家<span style='font-size:12px;font-weight:normal;'><a href='javascript:void(0);' class='showmystore'><img src="images/reg2.gif" />顯示 / 隱藏</a></span></div>
      </div><div>
        <table border="0" align="center" cellpadding="2" cellspacing="1">
          <tr>
            <td><div id="addrcontainer"></div></td>
            <td width="204" align="left"><span style="text-align:center; padding-bottom:10px; ">
              <input type="text" id="searchtxt" name="searchtxt" style="width:200px" class="theaddr" />
            </span></td>
            <td align="left"><span style="text-align:center; padding-bottom:10px; ">
              <input type="button" id="searchbtn" name="searchbtn" style="width:60px" value="搜尋店家" />
              <input type="hidden" id="poty" value="gm" />
              </span></td>
            </tr>
        </table>
        <div id="thestores"></div>
        <hr size="1" />
      <div id="tabsdiv" style="width:680px;height:auto; padding-left:10px;">
        <div title="店家資料" style="text-align:center;padding:10px;"><div id="storeprofile"></div></div>
       </div><hr size="1" />
        <div style="background-image:url(images/h002img_02.jpg);width:620px; height:40px; text-align:left; margin-left:30px;" id="mystores"><img src="images/h002img_01.jpg" alt="" width="63" height="37" /><div style=" position:relative;top:-30px; left:80px;font-size:16px; font-weight:bold; ">我最愛的店家(顯示20家) </div></div><div id="da-vinci-carousel"><p id="loadcarousel"></p>
<div id="da-vinci-title" ></div>
<div id="da-vinci-alt" ></div>
          
<div id="but1" class="carouselLeft" style="position:absolute;top:20px;right:64px;"></div>
<div id="but2" class="carouselRight" style="position:absolute;top:20px;right:20px;"></div>      
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
