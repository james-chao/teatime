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
<title>TeaTime 茶飲訂購系統 上傳店家-商品</title>
<link rel="stylesheet" type="text/css" href="css/easyui.css">
<link rel="stylesheet" type="text/css" href="css/icon.css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="js/jquery.easyui.min.js" type="text/javascript"></script>
<script src="js/easyui-lang-zh_TW.js" type="text/javascript"></script>
<script src="js/jquery.upload-1.0.2.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#importtabs').tabs();

	$('#suploadbutton').click(function(){
		var whi=$('#suploadcon input[checked=true]').val();
		$('#'+whi+'file').upload("importstore.php", {fn:whi},function(fdata){$('#suploaddiv').html(fdata);});
	});
	$('#puploadbutton').click(function(){
		var whi=$('#puploadcon input[checked=true]').val();
		$('#'+whi+'file').upload("importprod.php", {fn:whi},function(fdata){$('#puploaddiv').html(fdata);});
	});
	$('.OK').click(function(){
		$('#importtabs .imgNO').hide();	
		$('#importtabs .imgOK').show();	
	});
	$('.NO').click(function(){
		$('#importtabs .imgOK').hide();	
		$('#importtabs .imgNO').show();	
	});
	$('.ALL').click(function(){
		$('#importtabs .imgOK').show();	
		$('#importtabs .imgNO').show();	
	});
	
});

</script>
<style>
div li{list-style:square; }
</style>
</head>

<body>
<div style="text-align:left; font-size:11px;color:#999999;"><ul style="list-style:inside"><li>各項上傳店家、類別、商品功能是獨立的，故必須按各自的傳送按鍵才可上傳</li><li>上傳前，請先維護店家資料，若店家不存在，請新增店家。</li><li>請選擇要上傳店家、類別、商品的頁簽</li><li>第一列視為標頭列head，程式將skip掉。</li><li>請注意您上傳的資料是否有重覆。 </li><li>請確認商店名稱、類別名稱、商品名稱等與已存在的資料一致，不同字元視為不同項目。</li></ul></div><div id="importtabs" style="width:680px; height:auto;">
<div title="大量上傳店家資料" style="padding:10px;"><div style="text-align:left;padding:10px; font-size:11px;color:#999999;"><ul style="list-style:inside"><li>請選擇要上傳新店家的檔案格式</li><li>滑鼠移至<img src='css/images/tree_dnd_no.png' />可查看該行的問題</li><li>下載<font color="#FF0000">店家</font>範例格式檔案 [<a href="sample/uploadstore.xls">.XLS</a>] [<a href="sample/uploadstore.xlsx">.XLSX</a>] [<a href="sample/uploadstore.csv">.CSV</a>]</li></ul></div><div id="suploadcon" style="text-align:left; width:80%;"><input type="radio" name="sexcel" value="sexcel5" id="sexcelxls" /><label for="sexcel5file"> Excel 97~2003 (.xls) 格式上傳&nbsp;&nbsp;</label><input type="file" id="sexcel5file" name="sexcel5file" style="width:300px;" title="上傳Excel 97~2003格式" onChange="document.all.sexcelxls.checked=true;if(!/^.*(.xls)$/.test(this.value)){alert('此處上傳只允許.xls 格式')}" /><br /><input type="radio" name="sexcel" id="sexcel2007" value="sexcel2007" /><label for="sexcel2007file"> Excel 2007 (.xlsx) 格式上傳&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="file" id="sexcel2007file" name="sexcel2007file" style="width:300px;" title="上傳Excel 2007格式" onChange="document.all.sexcel2007.checked=true;if(!/^.*(.xlsx)$/.test(this.value)){alert('此處上傳只允許.xlsx 格式')}" /><br /><input type="radio" name="sexcel" value="sexcelcsv" id="sexcelcsv" /><label for="sexcelcsv"> Excel 逗號分隔 (.csv) 格式上傳 </label><input type="file" id="sexcelcsvfile" name="sexcelcsvfile" style="width:300px;" title="上傳Excel CSV格式" onChange="document.all.sexcelcsv.checked=true;if(!/^.*(.csv)$/.test(this.value)){alert('此處上傳只允許.csv 格式')}" /></div><p><input type='button' name='suploadbutton' id='suploadbutton' value='開始上傳店家' style='height:30pt; width:100pt;background-color:#C33; color:#FCF;' /></p><div style="text-align:left;"><img src="css/images/tree_file.gif" />上傳結果將顯示於下方:<input type="radio" name="OKNO" class="OK" /><img src='css/images/tree_dnd_yes.png' /><input type="radio" name="OKNO" class="NO" /><img src='css/images/tree_dnd_no.png' /><input type="radio" name="OKNO" class="ALL" checked="true" />顯示全部</div><div id="suploaddiv"></div></div>
<div title="大量上傳商品項目" style="padding:10px;"><div style="text-align:left;padding:10px; font-size:11px;color:#999999;"><ul style="list-style:inside"><li>請選擇要上傳新商品的檔案格式</li><li>滑鼠移至<img src='css/images/tree_dnd_no.png' />可查看該行的問題</li><li>下載<font color="#FF0000">商品</font>範例格式檔案 [<a href="sample/uploadprod.xls">.XLS</a>] [<a href="sample/uploadprod.xlsx">.XLSX</a>] [<a href="sample/uploadprod.csv">.CSV</a>]</li></ul></div><div id="puploadcon" style="text-align:left; width:80%;"><input type="radio" name="pexcel" value="pexcel5" id="pexcelxls" /><label for="pexcel5file"> Excel 97~2003 (.xls) 格式上傳&nbsp;&nbsp;</label><input type="file" id="pexcel5file" name="pexcel5file" style="width:300px;" title="上傳Excel 97~2003格式" onChange="document.all.pexcelxls.checked=true;if(!/^.*(.xls)$/.test(this.value)){alert('此處上傳只允許.xls 格式')}" /><br /><input type="radio" name="pexcel" id="pexcel2007" value="pexcel2007" /><label for="pexcel2007file"> Excel 2007 (.xlsx) 格式上傳&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="file" id="pexcel2007file" name="pexcel2007file" style="width:300px;" title="上傳Excel 2007格式" onChange="document.all.pexcel2007.checked=true;if(!/^.*(.xlsx)$/.test(this.value)){alert('此處上傳只允許.xlsx 格式')}" /><br /><input type="radio" name="pexcel" value="pexcelcsv" id="pexcelcsv" /><label for="pexcelcsv"> Excel 逗號分隔 (.csv) 格式上傳 </label><input type="file" id="pexcelcsvfile" name="pexcelcsvfile" style="width:300px;" title="上傳Excel CSV格式" onChange="document.all.pexcelcsv.checked=true;if(!/^.*(.csv)$/.test(this.value)){alert('此處上傳只允許.csv 格式')}" /></div><p><input type='button' name='puploadbutton' id='puploadbutton' value='開始上傳商品' style='height:30pt; width:100pt; background-color:#6F0; color:#060;' /></p><div style="text-align:left;"><img src="css/images/tree_file.gif" />上傳結果將顯示於下方:<input type="radio" name="OKNO" class="OK" /><img src='css/images/tree_dnd_yes.png' /><input type="radio" name="OKNO" class="NO" /><img src='css/images/tree_dnd_no.png' /><input type="radio" name="OKNO" class="ALL" checked="true" />顯示全部</div><div id="puploaddiv"></div></div></div>
</body>
</html>
