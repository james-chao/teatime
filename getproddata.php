<?
if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}
//if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']=="") exit;

require_once "conn.php";
$sno=$_REQUEST['sno'];
$cno=$_REQUEST['cno'];

if(!empty($cno)&& is_numeric($cno))
{


	global $link;
	$strSql="SELECT pno,pname,pprice,psize,ptemp,ptaste,ppic,pintro,psuit FROM ttprodtb WHERE sno=$sno AND cno=$cno";
	$data_rows=mysql_query($strSql,$link);
	
	$i=0;
	$maxcol=5;
	$tt=mysql_num_rows($data_rows);
	while(list($pno,$pname,$pprice,$psize,$ptemp,$ptaste,$ppic,$pintro,$psuit)=mysql_fetch_row($data_rows))
	{

		
		echo "<tr subid='DelProd$pno'><td><img subid='$pno' src='css/images/tree_dnd_no.png' alt='刪除此項' onclick='DelProd2(this);' /><input type='hidden' id='pno[]' name='pno[]' value='$pno' /></td>
<td><input type='text' id='pname[]' name='pname[]' value='$pname' /></td>
<td subid='sp'><input type='text' id='psize[]' name='psize[]' style='width:50px;' value='$psize' onclick='getthechk(this);' /></td>
<td subid='pp'><input type='text' id='pprice[]' name='pprice[]' style='width:40px;' value='$pprice' onclick='getthechk(this);' /></td>
<td subid='tp'><input type='text' id='ptemp[]' name='ptemp[]' style='width:50px;' value='$ptemp' onclick='getthechk(this);' /></td>
<td subid='ep'><input type='text' id='ptaste[]' name='ptaste[]' style='width:50px;' value='$ptaste' onclick='getthechk(this);' /></td>
<td><input type='file' id='ppic[]' name='ppic[]' style='width:190px;' src='$ppic' value='$ppic' /></td>
<td><input type='text' id='pintro[]' name='pintro[]' style='width:150px;' value='$pintro' /></td>
<td><input type='text' id='psuit[]' name='psuit[]' style='width:130px;' value='$psuit' /></td>
</tr>";
						
	}

echo "<script>
	$(function(){
		$('input[type=\"file\"]').filter(function(index) {
			return this.src != '';
		}).tooltip({delay: 0, showURL: false, bodyHandler: function() {return $('<img/>').attr('src', 'prodpic/'+this.src);}});
	});
</script>";
}
else
	echo "還沒有相關的產品"; //failure
?>