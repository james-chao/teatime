<?
header( 'Content-Type: text/html; charset=utf-8' );
if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require "conn.php";
$savebtn=$_REQUEST['savebutton'];

if($savebtn=="送出儲存")
{
	$pnoarr=$_REQUEST['pno'];
	$pnamearr=$_REQUEST["pname"];
	$ppricearr=$_REQUEST["pprice"];
	$pintroarr=$_REQUEST["pintro"];
	$psizearr=$_REQUEST["psize"];
	$ptastearr=$_REQUEST["ptaste"];
	$ptemparr=$_REQUEST["ptemp"];
	$psuitarr=$_REQUEST["psuit"];
	$strSql="";
	$n=0;
	 foreach ($pnoarr as $pno)
	 {
		if(!empty($pno)) 
		{
			$ppic=getprodpic($n);
			if($ppic!="") $ppic=",ppic='$ppic'"; //change if new pic selected otherwise keep it
			$strSql .="UPDATE ttprodtb SET pname='$pnamearr[$n]',pprice='$ppricearr[$n]',psize='$psizearr[$n]',ptemp='$ptemparr[$n]',ptaste='$ptastearr[$n]' $ppic,pintro='$pintroarr[$n]',psuit='$psuitarr[$n]' WHERE pno=$pno;";
			$n++;
		}
	 }
	$ret=mysqli_multi_query($link2,$strSql);

	if($ret==true)
	{
		echo "<script>alert('更新成功');</script>";
	}
	else
		echo "<script>alert('更新失敗,請稍後再試');</script>";
}
function getprodpic($m) 
{
	$ppicname=$_FILES["ppic"]["name"][$m];
	$ppicsize=$_FILES["ppic"]["size"][$m];
	$ppictype=$_FILES["ppic"]["type"][$m];
	
	if(!empty($ppicname))
	{
		if(($ppicsize/1024)<1500 && ($ppictype=="image/jpeg" || $ppictype=="image/pjpeg" || $ppictype=="image/gif"|| $ppictype=="image/x-png"))
		{
			$FilePath="prodpic/".$ppicname;
			move_uploaded_file($_FILES["ppic"]["tmp_name"][$m],$FilePath);
		}
		else
		{
			$ppicname="";
			echo "<script>alert('圖檔 $ppicname 上傳不成功，可能圖檔太大了，或是非jpg,gif,png圖檔格式!');</script>";
		}
	}
	else
		$ppicname="";
		
	return $ppicname;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TeaTime 茶飲訂購系統 商品維護</title>
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/easyui.css">
<link rel="stylesheet" type="text/css" href="css/icon.css">
<link rel="stylesheet" href="css/jquery.tooltip.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="js/jquery.easyui.min.js" type="text/javascript"></script>
<script src="js/easyui-lang-zh_TW.js" type="text/javascript"></script>
<script src="js/common.js" type="text/javascript"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script>
<script src="js/jquery.tooltip.js" type="text/javascript"></script>
<script src="js/jquery.upload-1.0.2.js" type="text/javascript"></script>
<script type="text/javascript">
var theobj;
$(function(){
	$('#tabsdiv').tabs();
	//$('#importtabs').tabs();
	$.post("searchstore.php",{srchtxt:""}, function(sdata){$('#thestores').html(sdata);});

	$('#searchbtn').click(function(){
		var searchtxt=$('#searchtxt').val();
		$.post("searchstore.php",{srchtxt:searchtxt}, function(sdata){$('#thestores').html(sdata);});
	});
	
	$("#addpddata a[title=pdl]").tooltip({bodyHandler:function(){ return $('#addprodlist').html();},showURL: false});
	
	$('a[iconCls="icon-ok"]').click(function(){
			var divid=$(theobj).parent().get(0).subid; 
			var ct=1;
			var v = "";
			//var s = "";
			var cnt = $('#'+divid+' input:checked').length;
			$('#'+divid+' input:checked').each(function(){
				v +=$(this).val();
				//s +=$(this).next('span').text();
				if(ct<cnt)
				{
					v +="-";
					//s +=",";
				}
				ct++;
				
			}).attr('checked',false); 
			
			//deal text value
			var getTxt=$('#'+divid+' :text').val();
			if(getTxt!="" && v!="") getTxt = "-"+getTxt;
	
			theobj.value=v+getTxt;
			
			$('#'+divid).window('close');
	});
	
	$('a[iconCls="icon-cancel"]').click(function(){$('.easyui-window').window('close');});
	
	$('.tabs-inner[subid="tab2"]').click(function(){
		var sno=$('#thestores input[@name=sno[]]:checked').val(); //the checked radio box
		if(sno=="" || sno==undefined) {alert('請先選擇店家');return false;}
	});
	$('.tabs-inner[subid="tab3"]').click(function(){
		var cno=$('#thecates input[@name="cno[]"]:checked').val(); //the checked radio box
		if(cno=="" || cno==undefined)
		{
			alert('請先選擇類別');
			return false;
		}
		else
		{
			var sno=$('#thestores input[@name=sno[]]:checked').val();
			$.post("getproddata.php",{sno:sno,cno:cno},function(data){$('#newProdZone').html(data);})
		}
	});

	
	$('#sidemenubtn').click(function(){
		var wh=$(this).attr('alt');
		if(wh=="開啟選單")
		{
			$('#sidemenu').slideDown(500);
			$(this).attr('alt','隱藏選單').attr('src','css/images/panel_tool_collapse.gif');
		}
		else
		{
			$('#sidemenu').fadeOut(2000);
			$(this).attr('alt','開啟選單').attr('src','css/images/panel_tool_expand.gif');
		}
	});
});

function DelCate(){ 
	var xidx=$(this).attr('subindex'); 
	var xalt=$(this).attr('alt'); 
	$('div[id="DelCate' + xidx + '"]').remove();
	$('#cnamestring').val($('#cnamestring').val().replace(xalt,''));

} 
function DelProd(img){ 
	$.messager.confirm('刪除商品', '確定從資料庫刪除?', function(r){
		if (r){
			var xidx=$(img).attr('subid');   
			$('tr[subid="DelProd' + xidx + '"]').remove();
			$.post("delprod.php",{pno:xidx},function(ret){if(ret==true) alert("刪除成功"); else alert("刪除失敗");})
		}
	});
} 
function setcateaddlb(newcate)
{
	$('#cateaddlb').html(newcate);
	$('#addprodlist').html('<span style=\"font-size:12px;color:#ff0000;\">此類別還沒有相關的產品</span>');
}

function getthechk(the)
{
	theobj=the;
	var divid=$(theobj).parent().get(0).subid; 
	//var divheight=$('#'+divid).css('height');
	//alert(divheight);
	//var of=$(theobj).offset();
	//$('#'+divid).show().offset({ top: of.top-18, left: of.left }).siblings().hide();
	$('#'+divid).show().window('open');
}

</script>
<style>
#maindivleft{ position:absolute;background-color:#fff; width:302px; height:288px; margin-left:50%; left:-510px;}
#maindivbtn{ position:absolute;background-color:#fff; width:458px; height:288px; margin-left:50%; left:-208px;}
#maindivright{ position:absolute;background-color:#fff; width:260px; height:288px; margin-left:50%; left:250px; background-image:url(images/menu_rightbg.jpg); background-repeat:no-repeat;}
#maindivbody{ position:absolute;background-color:#fff; width:1020px; height:auto; margin-left:50%; top:289px; left:-510px;}
.newpdtd td,th{ border:solid 1px #999999;}
#tabsdiv li{list-style:square; }
</style>
</head>

<body><div id="maindivleft"><table width="302" border="0" cellspacing="0" cellpadding="0">
    <tr><td><img src="images/main_1.jpg" width="302" height="127" alt="" /></td></tr>
    <tr><td><img src="images/main_4.jpg" width="302" height="161" alt="" /></td></tr></table></div>
<div id="maindivbtn">
<div style="background-image:url(images/main_menubg.jpg); background-repeat:no-repeat; width:458px; height:288px;">
<div id="bplbtndiv2" class="bplbtn"><img src="images/bplbtn_on.gif" /></div>
<div id="cpnbtndiv2" class="cpnbtn"><img src="images/cpnbtn_on.gif" /></div>
<div id="grlbtndiv2" class="grlbtn"><img src="images/grlbtn_on.gif" /></div>
<div id="ordbtndiv2" class="ordbtn"><img src="images/ordbtn_on.gif" /></div>
<div id="odlbtndiv2" class="odlbtn"><img src="images/odlbtn_on.gif" /></div>
<div id="gbbbtndiv2" class="gbbbtn"><img src="images/gbbbtn_on.gif" /></div>
<div id="membtndiv2" class="membtn"><img src="images/membtn_on.gif" /></div>
<div id="msgdiv"></div>
  </div></div>
<div id="maindivright">
  <div style="padding-top:10px;font-size:12px;color:#0000ff;font-weight:bold; cursor:pointer;"><img id="sidemenubtn" alt="開啟選單" src="css/images/panel_tool_expand.gif" />功能選單</div><div style="background-image:url(images/sidemenubg.gif); background-repeat:no-repeat;display:none;" id="sidemenu"><div style="padding-top:55px;"></div><div style="width:260px; height:260px; font-size:12px;padding-left:50px; line-height:16px" id="olinkdiv2"></div></div></div>
<div id="maindivbody">
  <table width="1020" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td colspan="3"><img src="images/main_9.gif" width="1020" height="13" /></td>
    </tr>
    <tr>
      <td width="17" valign="top" background="images/main_13.gif"><img src="images/main_10.gif" width="17" height="104" /></td>
      <td width="985" align="center"><div style="text-align:left;"><img src="images/title_editprod.gif" width="432" height="44" alt="" /></div><form method="post" enctype="multipart/form-data" name="form1" id="form1">
<div id="tabsdiv" style="width:930px;height:auto; padding-left:10px;">
<div title="1.選擇店家" style="padding:20px;"><div style="text-align:center; padding-bottom:10px; "><input type="text" id="searchtxt" name="searchtxt" style="width:200px" /> <input type="button" id="searchbtn" name="searchbtn" style="width:60px" value="搜尋店家" /><input type="hidden" id="poty" value="pd" /><br />(您可以輸入任意的關鍵字,如:店名、店家類型、送達地區、電話、地址、店家簡介等)</div><div id="thestores"></div></div>
<div title="2.選擇類別" style="padding:10px;"><div style="text-align:left; font-size:11px;color:#999999;"><ul style="list-style:inside"><li>請選擇要新增產品的類別</li><li>
若要新增的產品不在下列表中，可以選擇<span style="color:#060;font-size:14px; font-weight:bold;">+</span>新增</li></ul></div><div id="thecates" style="padding:10px;text-align:left;"></div></div>
<div id="addpddata" title="3.編輯商品項目" style="padding:20px;"><div style="text-align:left; font-size:11px;color:#999999;"><ul style="list-style:inside"><li>請輸入並選擇您的商品名稱及該商品的相關資料</li><li>若欄位不足，可以自行點選<span style="color:#060;font-size:14px; font-weight:bold;">+</span>新增</li></ul></div><p style="text-align:left; color:#060;"><img src="css/images/tree_checkbox_2.gif" />類別:<label id="cateaddlb"></label> <img src="css/images/tree_checkbox_2.gif" /><a title="pdl" href="javascript:void(0);">此類別現有商品</a></p><div id="addprodlist" style="display:none;"></div>
<div id="addproditem"><table border="0" cellspacing="1" cellpadding="1" width="99%" ><thead class="newpdtd"><tr><th>刪除</th><th>品名</th><th>大小</th><th>單價</th><th>冷熱</th><th>甜淡</th><th>圖片上傳</th><th>產品說明</th><th>特殊功效</th></tr></thead><tbody id="newProdZone"></tbody></table></div>
<p style="text-align:center"><input type='submit' name='savebutton' id='savebutton' value='送出儲存' style='height:30pt; width:50pt;' /></p></div>
</div>
  
    
  
</form></td>
      <td width="18" valign="top" background="images/main_14.gif"><img src="images/main_12.gif" width="18" height="104" /></td>
    </tr>
    <tr>
      <td colspan="3"><img src="images/main_15.gif" width="1020" height="16" /></td>
    </tr>
  </table>
</div><div id="ppmenu"><div id="sp" class="easyui-window" closed="true" modal="true" title="設定大小" style="text-align:center;display:none; background-color:#eee;border:solid 1px #999; width:168px; height:160px; font-size:12px;">
     	<input type="checkbox" value="大"><span>大</span>
        <input type="checkbox" value="中"><span>中</span>
        <input type="checkbox" value="小"><span>小</span>
        <input type="checkbox" value="特大"><span>特大</span><br />
        <input type="checkbox" value="特大-大-中-小"><span>以上全選</span><br /><br />
        <span>自訂:</span><input type="text" style='width:120px' title="可以自訂大小容量;請用減號-隔開">
        <p><a class="easyui-linkbutton" iconCls="icon-ok" href="javascript:void(0)">儲存</a><a class="easyui-linkbutton" iconCls="icon-cancel" href="javascript:void(0)">取消</a></p>

	</div>
    <div id="pp" class="easyui-window" closed="true" modal="true" title="設定單價" style="text-align:center;display:none; background-color:#eee;border:solid 1px #999; width:308px; height:160px; font-size:12px;">
		<p style='text-align:left; padding:5px;'>產品的單價常會因容量、體積、重量等大小而不同，<br />此時請用 - 減號將物品的單價區隔。例: 50-40-35 <br /></p>
        <span>自訂:</span><input type="text" style='width:220px' title="單價要與您在「大小」欄位的個數相同;請用減號-隔開">
        <p><a class="easyui-linkbutton" iconCls="icon-ok" href="javascript:void(0)">儲存</a><a class="easyui-linkbutton" iconCls="icon-cancel" href="javascript:void(0)">取消</a></p>

	</div>
    <div id="tp" class="easyui-window" closed="true" modal="true" title="設定冷熱" style="text-align:center;display:none; background-color:#eee;border:solid 1px #999; width:258px; height:160px; font-size:12px;">
        <input type="checkbox" value="正常"><span>正常</span>
        <input type="checkbox" value="少冰"><span>少冰</span>
        <input type="checkbox" value="去冰"><span>去冰</span>
        <input type="checkbox" value="溫的"><span>溫的</span>
        <input type="checkbox" value="熱的"><span>熱的</span><br />
        <input type="checkbox" value="正常-少冰-去冰-溫的-熱的"><span>以上全選</span><br /><br />
        <span>自訂:</span><input type="text" style='width:120px' title="可以自訂冷熱程度;請用減號-隔開">
        <p><a class="easyui-linkbutton" iconCls="icon-ok" href="javascript:void(0)">儲存</a><a class="easyui-linkbutton" iconCls="icon-cancel" href="javascript:void(0)">取消</a></p>
	</div>
    <div id="ep" class="easyui-window" closed="true" modal="true" title="設定甜淡" style="text-align:center;display:none; background-color:#eee;border:solid 1px #999; width:258px; height:210px; font-size:12px;">
    	<input type="checkbox" value="無糖"><span>無糖</span>
        <input type="checkbox" value="3分"><span>3分</span>
        <input type="checkbox" value="5分"><span>5分</span>
        <input type="checkbox" value="7分"><span>7分</span>
        <input type="checkbox" value="全糖"><span>全糖</span><br />
        <input type="checkbox" value="無糖-3分-5分-7分-全糖"><span>以上全選</span><br /><br />
        <input type="checkbox" value="大辣"><span>大辣</span>
        <input type="checkbox" value="中辣"><span>中辣</span>
        <input type="checkbox" value="小辣"><span>小辣</span><br />
        <input type="checkbox" value="大辣-中辣-小辣"><span>以上全選</span><br /><br />
       
        <span>自訂:</span><input type="text" style='width:120px' title="可以自訂甜淡口味;請用減號-隔開">
        <p><a class="easyui-linkbutton" iconCls="icon-ok" href="javascript:void(0)">儲存</a><a class="easyui-linkbutton" iconCls="icon-cancel" href="javascript:void(0)">取消</a></p>
	</div></div>
</body>
</html>
