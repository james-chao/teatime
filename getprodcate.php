<?
header( 'Content-Type: text/html; charset=utf-8' );
if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}
//if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']=="") exit;

require_once "conn.php";
$sno=$_POST['sno'];

if(!empty($sno))
{
	global $link;
	$strSql="SELECT cno,cname,sowner FROM ttcatetb C INNER JOIN ttstoretb S ON C.sno=S.sno WHERE C.sno=$sno";
	$data_rows=mysql_query($strSql,$link);
	
	$i=1;
	while(list($cno,$cname,$sowner)=mysql_fetch_row($data_rows))
	{
		if($sowner==$_COOKIE['teatimeuuser'] || $_COOKIE['teatimeutype']==2)
			echo "<span id='span$cno'><input type='radio' class='cno' id='cno[]' name='cno[]' value='$cno' title='$cname' /><span class='edittext' id='$cno'>$cname</span><img src='css/images/tree_dnd_no.png' class='rmbtn' id='$cno' /></span>";
		else
			echo "<input type='radio' class='cno' id='cno[]' name='cno[]' value='$cno' title='$cname' />$cname";
		//if($i==10) echo "<br />";
		$i++;
		//$cnamestring .=$cname."|";
	}
	echo "<input type='hidden' id='cnamestring' name='cnamestring' value='$cnamestring' />";
}
else
	echo ""; //failure
?>
<script type="text/javascript" src="js/jquery.ui.js"></script>
<script src="js/jquery.editinplace.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
	$('.cno').click(function(){
		$('#cateaddlb').text($(this).attr('title'));
		var cno=$(this).val();
		$.post("getproduct.php",{cno:cno}, function(cdata){$('#addprodlist').html(cdata);});
	});		
	
	//show image tip
	$('.tonus').tooltip({delay: 0, showURL: false, bodyHandler: function() {return $("<img/>").attr("src", this.src);}});
	$(".edittext").editInPlace({
		url: "passnewcate.php",
		saving_text:"儲存中..."
	});
	$('.rmbtn').click(function(){
		var sno=$('.sno:checked').val();	
		var cno=$(this).attr('id');
		$.messager.confirm('再次確認', '您確定要刪除這個類別嗎?', function(r){
			if(r) {
				$.post("delcate.php",{sno:sno,cno:cno}, function(r){(r)?$('#span'+cno).remove():alert('刪除失敗');});
				return false;
			}
		});
	});
});
</script>