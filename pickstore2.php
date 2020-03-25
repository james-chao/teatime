<?
session_start();
if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";
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
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

<script type="text/javascript" src='js/jquery.autocomplete.js'></script>

<script type="text/javascript">

$(document).ready(function(){
	$('#ordertitle').autocomplete({
			width: 400,
			delimiter: /(,|;)\s*/,
			serviceUrl:"getmaillist.php"
	});
	
});

</script>

<style>
#friendlist{border:solid 1px #0099FF;width:500px;height:150px; overflow:scroll; padding:5px;padding-top:0px;}
#profiletb td{ border:solid 1px #999999;}
</style>
</head>
<BODY>
<div id="topdiv"><div style="width:385px;"><img src="images/sub01_02.jpg" width="385" height="220" alt=""></div><div style="width:385px;  position:relative; left:385px; top:-220px;"><img src="images/sub01_04.jpg" width="385" height="220" alt=""></div></div>
<div id="leftdiv"><div style="width:50px; height:149px; "><img src="images/sub01_01.jpg" width="250" height="149" id="getT" /></div><div id="menubtndiv" style="width:250px; height:490px; background-image:url(images/menubg.jpg); background-repeat:no-repeat"><div id="bplbtndiv" class="bplbtn"><img src="images/bplbtn_on.gif" /></div><div id="odlbtndiv" class="odlbtn"><img src="images/odlbtn_on.gif" /></div><div id="grlbtndiv" class="grlbtn"><img src="images/grlbtn_on.gif" /></div><div id="ordbtndiv" class="ordbtn"><img src="images/ordbtn_on.gif" /></div><div id="cpnbtndiv" class="cpnbtn"><img src="images/cpnbtn_on.gif" /></div><div id="membtndiv" class="membtn"><img src="images/membtn_on.gif" /></div><div id="gbbbtndiv" class="gbbbtn"><img src="images/gbbbtn_on.gif" /></div><div id="olinkdiv"></div></div><? require "friendstab.php";?></div>
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
      <td valign="top"><form action="saveorder.php" enctype="multipart/form-data" method="post" id="form1"><div style="background-image:url(images/h002img_02.jpg);width:620px; height:40px; text-align:left; margin-left:30px;" id="mystores"><img src="images/h002img_01.jpg" alt="" width="63" height="37" /><div style=" position:relative;top:-30px; left:80px;font-size:16px; font-weight:bold; ">我最愛的店家(顯示20家) <span style='font-size:12px;font-weight:normal;'><a href='javascript:void(0);' class='showmystore'><img src="images/reg2.gif" />顯示 / 隱藏</a></span></div></div><div id="da-vinci-carousel"><p id="loadcarousel"></p>
<div id="da-vinci-title" ></div>
<div id="da-vinci-alt" ></div>
          
<div id="but1" class="carouselLeft" style="position:absolute;top:20px;right:64px;"></div>
<div id="but2" class="carouselRight" style="position:absolute;top:20px;right:20px;"></div>      
</div><hr size="1" />
        <div style="background-image:url(images/h002img_02.jpg);width:620px; height:40px; text-align:left; margin-left:30px;"><img src="images/h002img_01.jpg" alt="" width="63" height="37" />
          <div style=" position:relative;top:-35px; left:80px;font-size:16px; font-weight:bold; ">找尋要發起邀約的店家<span style='font-size:12px;font-weight:normal;'><a href='javascript:void(0);' class='showstores'><img src="images/reg2.gif" />顯示 / 隱藏</a></span></div>
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
        <div title="步驟1.店家資料" style="text-align:center;padding:10px;"><div id="storeprofile"></div></div>
        <div title="步驟2.選擇商品" style="text-align:center;padding:10px;"><div id="storeproduct"></div></div>
        <div title="步驟3.填寫邀約訂單" style="text-align:center;padding:10px;">
          <table  border="0" cellpadding="2" cellspacing="1" bgcolor="#666666" width="600" align="center">
            <tbody>
              <tr>
                <td height="40" colspan="2" align="center" bgcolor="#FFFFCC"><strong>邀約資訊</strong></td>
              </tr>
              <tr>
                <td height="30" align="right" bgcolor="#FFFFFF">標題:</td>
                <td align="left" bgcolor="#FFFFFF"><input name="ordertitle"  id="ordertitle" size="60" class="easyui-validatebox" required="true" /></td>
              </tr>
              <tr>
                <td height="30" align="right" bgcolor="#FFFFFF">發起人:</td>
                <td align="left" bgcolor="#FFFFFF"><input  maxlength="20" size="20" name="nickname" id="nickname" value="<?=$_COOKIE['teatimeunick'];?>" required="true" class="easyui-validatebox" />
                  <input  type="checkbox" name="flchkbox" id="flchkbox" value="1" />
                  加入我的朋友</td>
              </tr>
              <tr id="fltr" style='display:none;'>
                <td height="30" align="right" bgcolor="#FFFFFF">我的朋友:</td>
                <td align="left" bgcolor="#FFFFFF"><input name="ufname" id="ufname" style="width:420px" />&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" id="flclose" ><img alt='朋友列表' title='朋友列表' border="0" src='css/images/tabs_close.gif' />朋友列表</a><input name="ufno" id="ufno" type="hidden" /><div  id="friendlist"><? 

$strSql="SELECT U.uno,uname,unick,upic,ufno FROM ttfriendtb F INNER JOIN ttusertb U ON F.ufno=U.uno WHERE uonoff='y' AND fagree='y' AND F.uno=$uno UNION SELECT U.uno,uname,unick,upic,ufno FROM ttfriendtb F INNER JOIN ttusertb U ON F.uno=U.uno WHERE uonoff='y' AND fagree='y' AND F.ufno=$uno";
$data_rows=mysql_query($strSql);

while(list($ufno,$uname,$unick,$upic,$ufno)=mysql_fetch_row($data_rows))
{
	echo "<div style='width:30px;height:25px;float:left;margin:1px;cursor:pointer'><img alt='邀請 $unick' title='邀請 $unick' src='userpic/$upic' width='25' height='25' class='add2buy' ufno='$ufno' ufnick='$unick' /></div>";
	
}
?></div></td>
              </tr>
              <tr align="middle">
                <td height="30" align="right" bgcolor="#FFFFFF">終止時間:</td>
                <td align="left" bgcolor="#FFFFFF"><input  class="easyui-datetimebox" required="true" style="width:160px" name="deadline" id="deadline" />
                  (24小時制: mm/dd/yyyy 例: 04/01/2010 23:11:13)</td>
              </tr>
              <tr>
                <td height="30" align="right" bgcolor="#FFFFFF">循環周期:</td>
                <td align="left" bgcolor="#FFFFFF"> <input type="radio" name="cycle" id="byone" value="byone" checked="checked" /><label id="byonelb" style="color:#F00" for="byone">單次邀約</label><input type="radio" name="cycle" id="bydays" value="bydays" />每<input  style="width:25px" name="duration" id="duration" />天 / <input type="radio" name="cycle" value="byweeks" id="byweeks"/>每星期<input  type="checkbox" name="week[]" id="week[]" value="0" />日<input  type="checkbox" name="week[]" id="week[]" value="1" />一<input  type="checkbox" name="week[]" id="week[]" value="2" />二<input  type="checkbox" name="week[]" id="week[]" value="3" />三<input  type="checkbox" name="week[]" id="week[]" value="4" />四<input  type="checkbox" name="week[]" id="week[]" value="5" />五<input type="checkbox" name="week[]" id="week[]" value="6" />六</td>
              </tr>
              <tr align="middle">
                <td height="30" align="right" bgcolor="#FFFFFF">邀約說明:</td>
                <td align="left" bgcolor="#FFFFFF"><textarea name="ordernote" cols="60" rows="4" id="ordernote"></textarea></td>
              </tr>
              <tr align="middle">
                <td height="30" align="right" bgcolor="#FFFFFF">E-Mail:</td>
                <td align="left" bgcolor="#FFFFFF"><input  maxlength="40" size="40" name="useremail" id="useremail" value="<?=$_COOKIE['teatimeuuser'];?>" class="easyui-validatebox" validType="email"></ />
                  (結單後自動寄信用)</td>
              </tr>
              <tr align="middle">
                <td height="30" colspan="2" align="center" bgcolor="#FFFFFF"><input style="width:80px;height:40px;"  type="submit" value="建立邀約" name="Submit" /></td>
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
