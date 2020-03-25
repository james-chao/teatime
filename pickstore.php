<?
include_once(dirname(__FILE__) . '/Classes/utility.php');

if (!$ret){
	echo "<script>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

$uno=$_COOKIE['teatimeuserno'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TeaTime 茶飲訂購系統 發起邀約</title>
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link href="css/cloudcarousel.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/easyui.css">
<link rel="stylesheet" type="text/css" href="css/icon.css">
<link rel="stylesheet" href="css/jquery.tooltip.css" />
<link type="text/css" href="css/jquery.rater.css" rel="stylesheet"/>
<link rel="stylesheet" type="text/css" href="css/jquery.cleditor.css" />
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
<script type="text/javascript" src='js/jquery.autocomplete.js'></script>
<script type="text/javascript" src="js/jquery.cleditor.min.js"></script>
<script type="text/javascript">

$(document).ready(function(){
	$.post("loadcarousel.php", function(data){
		if(data=="") data="<div style='color:#ff0000;text-align:center;'>您尚未新增任何店家!!</div>";
		$('#loadcarousel').html(data);
	});
	$('#addrcontainer').twzipcode();
	$('.thezip').hide();
	$('#tabsdiv').tabs();
	$.post("searchstore.php", function(sdata){$('#thestores').html(sdata);});
	$('#searchbtn').click(function(){
		var thecity=$('.thecity option:selected').val();
		var thecounty=$('.thecounty option:selected').val();
		var searchtxt=$('#searchtxt').val();
		
		//if(ShopMapParam.zoomLevel==0)
		$.post("searchstore.php",{srchtxt:searchtxt,city:thecity,county:thecounty}, function(sdata){$('#thestores').html(sdata);});
		/*else
		{
			if(!thecity || thecity==null) thecity="";
			if(!thecounty || thecounty==null) thecounty="";
			ShopMap.addressGps(thecity,thecounty,searchtxt);
		}*/
	});
	
	var bftchk=false,bstchk=true;
	$('.showmystore').click(function(){
		if(bftchk==false){
			bftchk=true; 
			$(this).text('隱藏'); 
		}else{ 
			bftchk=false;
			$(this).text('顯示');
		}
		$('#da-vinci-carousel').slideToggle();
	});
	$('.showstores').click(function(){
		if(bstchk==false){
			bstchk=true; 
			$(this).text('隱藏'); 
		}else{ 
			bstchk=false;
			$(this).text('顯示');
		}
		$('#storelistdiv').slideToggle();
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
	$('#btnsubmit').click(function(){
		var errmsg="";
		
		if($('input[name=selsno]')==null || $('input[name=selsno]').attr("id")==undefined) errmsg +="您尚未選擇店家\n";
		if($('#ordertitle').val()=="" ) errmsg +="您尚未填入邀約標題\n";
		if($("input[name=deadline]").val()=="" ) errmsg +="您尚未填入邀約終止時間\n";
		if($('#bydays').attr('checked') && $('#duration').val()=="" ) errmsg +="您尚未填入所選的周期數值\n";
		//return $('#myform').form('validate');
		if(errmsg=="")
		{
			document.getElementsByTagName("myform").submit();
			return true;
		}
		else
		{
			alert(errmsg);
			return false;
		}
	}); 
	
	$.extend($.fn.validatebox.defaults.rules, {
		minLength: {
			validator: function(value, param){
			return value.length >= param[0];
			},
			message: '至少{0}個數值'
		},
		isnumber: {
			validator: function(value, param){
			return /^-{0,1}\d*\.{0,1}\d+$/.test(value);
			},
			message: '必須是整數值'
		}
    });

	$('#flchkbox').click(function(){
		$('#fltr').slideToggle();
	});
	$('#flclose').click(function(){
		$('#friendlist').slideToggle();
	});
	/*$('#ufname').change(function(){
		var ufname=$('#ufname').val();
		if(ufname.length==0) 
		{
			$('#ufno').val('');
		}
		else
		{
			$.post("getfriendsufno.php",{ufname:ufname},function(data){
				$('#ufno').val(data);
			});
		}
	});*/
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

function IsNumeric(input){
    var RE = /^-{0,1}\d*\.{0,1}\d+$/;
    return (RE.test(input));
}

</script>

<style>
#friendlist{border:solid 1px #0099FF;width:500px;height:150px; overflow:scroll; padding:2px;padding-top:0px;}
#profiletb td{ border:solid 1px #999999;}
.friendpic{width:40px;height:40px;float:left;margin:1px;border:1px solid #eee;cursor:pointer}
.divcenter{text-align:center;}
</style>
</head>
<BODY>
<div id="topdiv"><div style="width:385px;"><img src="images/sub01_02.jpg" width="385" height="220" alt=""></div><div style="width:385px;  position:relative; left:385px; top:-220px;"><img src="images/sub01_04.jpg" width="385" height="220" alt=""></div></div>
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
<div id="newsdiv"><span style="text-align:left;"><img src="images/title_ordermenu.gif" /></span></div>
<div id="rightdiv">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="47" valign="top" background="images/sub01_19.gif"><img src="images/sub01_07-08.gif" width="47" height="28" alt="" /></td>
      <td valign="top" style="background-image:url(images/sub01_09.gif); background-position:top;">&nbsp;</td>
      <td width="24" height="28" valign="top" background="images/sub01_14.gif"><img src="images/sub01_10.gif" width="24" height="28" alt="" /></td>
    </tr>
    <tr>
      <td valign="top" background="images/sub01_19.gif"><img src="images/sub01_09-13.gif" width="47" height="121" alt="" /></td>
      <td valign="top"><form action="saveorder.php" enctype="multipart/form-data" method="post" name="myform" id="myform"><div style="background-image:url(images/h002img_02.jpg);width:620px; height:40px; text-align:left; margin-left:30px;" id="mystores"><img src="images/h002img_01.jpg" alt="" width="63" height="37" /><div style=" position:relative;top:-30px; left:80px;font-size:16px; font-weight:bold; ">我最愛的店家(顯示20家) <span style='font-size:12px;font-weight:normal;'><img src="images/reg2.gif" />點此<a href='javascript:void(0);' class='showmystore'>顯示</a>我最愛的店家</span></div></div><div id="da-vinci-carousel" style="display:none;"><p id="loadcarousel"></p>
<div id="da-vinci-title" ></div>
<div id="da-vinci-alt" ></div>
          
<div id="but1" class="carouselLeft" style="position:absolute;top:20px;right:64px;"></div>
<div id="but2" class="carouselRight" style="position:absolute;top:20px;right:20px;"></div>      
</div><hr size="1" />
        <div style="background-image:url(images/h002img_02.jpg);width:620px; height:40px; text-align:left; margin-left:30px;"><img src="images/h002img_01.jpg" alt="" width="63" height="37" />
          <div style=" position:relative;top:-35px; left:80px;font-size:16px; font-weight:bold; ">找尋要發起邀約的店家<span style='font-size:12px;font-weight:normal;'><img src="images/reg2.gif" />點此<a href='javascript:void(0);' class='showstores'>隱藏</a>下列店家</span></div>
      </div><div id="storelistdiv">
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
        <div id="thestores">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><div id="googleMap" style="width:470px; height: 250px"></div></td>
              <td><div id="storelist" style="width:225px; height: 250px"></div></td>
              </tr>
            </table>
        </div>
      </div><hr size="1" />
       <div style="background-image:url(images/h002img_02.jpg);width:620px; height:40px; text-align:left; margin-left:30px;"><img src="images/h002img_01.jpg" alt="" width="63" height="37" />
          <div style=" position:relative;top:-25px; left:80px;font-size:16px; font-weight:bold; ">選擇商品 - 開立邀約單</div>
      </div><div>
      <div id="tabsdiv" style="width:680px;height:auto; padding-left:10px;">
        <div title="步驟1.店家資料" style="text-align:center;padding:10px;"><div id="storeprofile">***請依上方列表選擇一個店家***</div></div>
        <div title="步驟2.選擇商品" style="text-align:center;padding:10px;"><div id="storeproduct">***請選擇本次邀約的商品***<hr size="1" />1.若您未見到任何商品，請先點選上方店家。<br />
        2.若仍未顯示商品，表示此店家商品尚未維護<br />
        3.若已有維護，代表您的網路可能無法連線api</div></div>
        <div title="步驟3.填寫邀約訂單" style="text-align:center;padding:10px;">
          <table  border="0" cellpadding="2" cellspacing="1" bgcolor="#666666" width="600" align="center">
            <tbody>
              <tr>
                <td height="40" colspan="2" align="center" bgcolor="#FFFFCC"><strong>邀約資訊</strong></td>
              </tr>
              <tr>
                <td height="30" align="right" bgcolor="#FFFFFF">標題:</td>
                <td align="left" bgcolor="#FFFFFF"><input name="ordertitle"  id="ordertitle" size="60" class="easyui-validatebox" /></td>
              </tr>
              <tr>
                <td height="30" align="right" bgcolor="#FFFFFF">發起人:</td>
                <td align="left" bgcolor="#FFFFFF"><input  maxlength="20" size="20" name="nickname" id="nickname" value="<?=$_COOKIE['teatimeunick'];?>" readonly /></td>
              </tr>
              <tr>
                <td height="30" align="right" bgcolor="#FFFFFF">受邀人:</td>
                <td align="left" bgcolor="#FFFFFF"><textarea name="ufname" id="ufname" style="font-size:11px;width:400px;height:40px;"></textarea>&nbsp;&nbsp;&nbsp;<input  type="checkbox" name="flchkbox" id="flchkbox" value="1" />從朋友清單加入<br /><span style='font-size:12px;color:#666'>PS.請填公司通訊錄之個人或群組不含Email的完整名稱，如:someone (某米人)<span style="color:#00F">,</span>*******總經理室<span style="color:#F00">;</span>John.O(醬喔)</span>
                  </td>
              </tr>
              <tr id="fltr" style='display:none;'>
                <td height="30" align="right" bgcolor="#FFFFFF">我的朋友:</td>
                <td align="left" bgcolor="#FFFFFF"><a href="javascript:void(0);" id="flclose" ><img alt='朋友列表' title='朋友列表' border="0" src='css/images/tabs_close.gif' />開啟 / 隱藏</a><input name="ufemail" id="ufemail" type="hidden" /><div  id="friendlist">
				
<?php

$strSql="SELECT U.uuser,uname,unick,upic,ufno FROM ttfriendtb F INNER JOIN ttusertb U ON F.ufno=U.uno WHERE uonoff='y' AND fagree='y' AND F.uno=$uno UNION SELECT U.uno,uname,unick,upic,ufno FROM ttfriendtb F INNER JOIN ttusertb U ON F.uno=U.uno WHERE uonoff='y' AND fagree='y' AND F.ufno=$uno";
$util->db->Query($strSql);

while(!$util->db->EndOfSeek())
{
	$imgsrc=$util->picurl."quanta_".$upic."_MThumb.jpg";
	if(!$util->isPhotoExist($imgsrc)) $imgsrc="userpic/nophoto.png";
	$user=$util->db->RowArray();
	echo "<div class='friendpic'><div class='divcenter'><img title='邀請 ".$user['unick']."' src='$imgsrc' width='25' height='25' class='add2buy' ufemail='".$user['uuser']."' ufnick='".$user['unick']."' /></div><div class='divcenter'>".$user['uname']."</div></div>";
}
?>
</div></td>
              </tr>
              <tr align="middle">
                <td height="30" align="right" bgcolor="#FFFFFF">終止時間:</td>
                <td align="left" bgcolor="#FFFFFF"><input  class="easyui-datetimebox" style="width:160px" name="deadline" id="deadline" />
                  (24小時制: mm/dd/yyyy 例: 04/01/2010 23:11:13)</td>
              </tr>
              <tr>
                <td height="30" align="right" bgcolor="#FFFFFF">循環周期:</td>
                <td align="left" bgcolor="#FFFFFF"> <input type="radio" name="cycle" id="byone" value="byone" checked="checked" /><label id="byonelb" style="color:#F00" for="byone">單次邀約</label><input type="radio" name="cycle" id="bydays" value="bydays" />每<input  style="width:25px" name="duration" id="duration" />天 / <input type="radio" name="cycle" value="byweeks" id="byweeks"/>每星期<input  type="checkbox" name="week[]" id="week[]" value="0" />日<input  type="checkbox" name="week[]" id="week[]" value="1" />一<input  type="checkbox" name="week[]" id="week[]" value="2" />二<input  type="checkbox" name="week[]" id="week[]" value="3" />三<input  type="checkbox" name="week[]" id="week[]" value="4" />四<input  type="checkbox" name="week[]" id="week[]" value="5" />五<input type="checkbox" name="week[]" id="week[]" value="6" />六</td>
              </tr>
              <tr align="middle">
                <td height="30" align="right" bgcolor="#FFFFFF">邀約說明:</td>
                <td align="left" bgcolor="#FFFFFF"><textarea class="txtar" name="ordernote" cols="60" rows="4" id="ordernote"></textarea></td>
              </tr>
              <tr align="middle">
                <td height="30" align="right" bgcolor="#FFFFFF">E-Mail:</td>
                <td align="left" bgcolor="#FFFFFF"><input  maxlength="40" size="40" name="useremail" id="useremail" value="<?=$_COOKIE['teatimeuuser'];?>" class="easyui-validatebox" validType="email"></ />
                  (結單後自動寄信用)</td>
              </tr>
              <tr align="middle">
                <td height="30" colspan="2" align="center" bgcolor="#FFFFFF"><input style="width:80px;height:40px;"  type="submit" value="建立邀約" name="btnsubmit" id="btnsubmit" /></td>
              </tr>
            </tbody>
          </table>
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
