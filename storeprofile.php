<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
.addustore,.delustore{
	border:0px;
}
.addustore:hover,.delustore:hover{
	border:dotted 2px #666;
}
</style>
<?
if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";

$sno=$_REQUEST['sno'];
if($sno)
{
	$strSql="SELECT * FROM ttstoretb WHERE sno=$sno";
	$data_rows=mysql_query($strSql,$link);

	if(list($sno,$sname,$sgrade,$srater,$sintro,$szip,$scity,$scounty,$saddr,$sgps,$surl,$spic,$stel,$sfax,$sdispatch,$stype,$sowner,$lastupdate)=mysql_fetch_row($data_rows))
	{
		$countrater=count(explode(",",$srater))-1;
		if($countrater>0) $star=floor($sgrade/$countrater); else $star=0;
		echo "<table width='550' border='0' cellpadding='4' cellspacing='1' id='profiletb'>
  <tr>
    <td width='100' align='right'>最愛管理:</td>
    <td align='left'><a class='addustore' href='javascript:void(0);' ><img src='images/addmystore.gif' /></a> <a class='delustore' href='javascript:void(0);' ><img src='images/delmystore.gif' /></a></td>
  </tr>
  <tr>
    <td width='100' align='right'>店名:</td>
    <td align='left'><label id='stnamel'>$sname</label><input type='hidden' id='selsno' name='selsno' value='$sno' /></td>
  </tr>
  <tr>
    <td align='right'>目前評價:</td>
    <td align='left'><div id='storerate'></div>(已有 $countrater 人評價此店家)</td>
  </tr>
  <tr>
    <td align='right'>聯絡電話:</td>
    <td align='left'><label id='sttell'>$stel</label></td>
  </tr>
  <tr>
    <td align='right'>傳真:</td>
    <td align='left'><label id='stfaxl'>$sfax</label></td>
  </tr>
  <tr>
    <td align='right'>店家網址:</td>
    <td align='left'><label id='sturll'><a href='$surl'>$surl</a>&nbsp;</label></td>
  </tr>
  <tr>
    <td align='right' valign='top'>聯絡地址:</td>
    <td align='left'><label id='staddrl'>($szip$scity$scounty) $saddr</label></td>
  </tr>
  <tr>
    <td align='right'>類型:</td>
    <td align='left'><label id='sttypel'>$stype</label></td>
  </tr>
  <tr>
    <td align='right'>送達地區:</td>
    <td align='left'><label id='stdispatchl'>$sdispatch</label></td>
  </tr>
  <tr>
    <td align='right' valign='top'>店家圖片:</td>
    <td align='left' valign='top'><label id='stpicl'><img src='storepic/$spic' width'20' height='20' /></label></td>
  </tr>
  <tr>
    <td align='right'>簡介與規則:</td>
    <td align='left'><label id='stintrol'>$sintro</label></td>
  </tr>
  <tr>
    <td align='right'>建立者:</td>
    <td align='left'><label>$sowner</label></td>
  </tr>
  <tr>
    <td align='right'>最後更新:</td>
    <td align='left'><label>$lastupdate</label></td>
  </tr>
</table>";



echo "<script type='text/javascript'>
$(document).ready(function(){
	$('#storerate').rater('ratestore.php?sno=$sno' , {maxvalue:5,curvalue:$star} , function(el , value , res) {
	   //el.append(res+;<br />;);
	   (res==1) ?alert('你給此店家評價'+value+'分'):alert('你已評價過此店家');  
	});
	$('label:empty').html('&nbsp;');
	$('.addustore').click(function(){
		$.post('addustore.php',{sno:$sno}, function(adata){
			if(adata) $.post('loadcarousel.php', function(data){
				alert('新增成功');
				$('#loadcarousel').html(data);
			});
		else 
			alert('新增失敗');
		});	
	});
	$('.delustore').click(function(){
		$.post('delustore.php',{sno:$sno}, function(rdata){
			if(rdata) $.post('loadcarousel.php', function(data){
				alert('移除成功');
				$('#loadcarousel').html(data);
			});
		else 
			alert('移除失敗');
		});	
	});
});
</script>";
	}
}

?>